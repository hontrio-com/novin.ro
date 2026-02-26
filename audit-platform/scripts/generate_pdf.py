#!/usr/bin/env python3
"""
Inovex Audit PDF Generator - cu suport diacritice romanesti
Usage: python generate_pdf.py <audit_json_file> <output_pdf_file>
"""
import sys
import json
import os
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.units import mm
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle,
    HRFlowable, KeepTogether, PageBreak
)
from reportlab.pdfgen import canvas
from reportlab.lib.enums import TA_LEFT, TA_CENTER, TA_RIGHT
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont

# ── FONT SETUP ──────────────────────────────────────
def setup_fonts():
    """
    Inregistreaza fonturile cu suport pentru diacritice romanesti.
    Cauta fonturile DejaVu in mai multe locatii posibile.
    Pune fonturile DejaVu in scripts/fonts/ pentru cel mai bun rezultat.
    """
    script_dir = os.path.dirname(os.path.abspath(__file__))

    search_paths = [
        os.path.join(script_dir, 'fonts'),
        r'C:\Windows\Fonts',
        os.path.expanduser(r'~\AppData\Local\Microsoft\Windows\Fonts'),
        '/usr/share/fonts/truetype/dejavu',
        '/usr/share/fonts/dejavu',
        '/usr/local/share/fonts',
        '/Library/Fonts',
        '/System/Library/Fonts',
    ]

    font_files = {
        'Regular':    'DejaVuSans.ttf',
        'Bold':       'DejaVuSans-Bold.ttf',
        'Italic':     'DejaVuSans-Oblique.ttf',
        'BoldItalic': 'DejaVuSans-BoldOblique.ttf',
    }

    found = {}
    for style, filename in font_files.items():
        for path in search_paths:
            full = os.path.join(path, filename)
            if os.path.exists(full):
                found[style] = full
                break

    if len(found) == 4:
        pdfmetrics.registerFont(TTFont('Roman', found['Regular']))
        pdfmetrics.registerFont(TTFont('Roman-Bold', found['Bold']))
        pdfmetrics.registerFont(TTFont('Roman-Italic', found['Italic']))
        pdfmetrics.registerFont(TTFont('Roman-BoldItalic', found['BoldItalic']))
        from reportlab.lib.fonts import addMapping
        addMapping('Roman', 0, 0, 'Roman')
        addMapping('Roman', 1, 0, 'Roman-Bold')
        addMapping('Roman', 0, 1, 'Roman-Italic')
        addMapping('Roman', 1, 1, 'Roman-BoldItalic')
        return 'Roman'

    # Fallback Vera (inclus in reportlab)
    try:
        import reportlab
        rl_fonts = os.path.join(os.path.dirname(reportlab.__file__), 'fonts')
        vera = {
            'Regular':    os.path.join(rl_fonts, 'Vera.ttf'),
            'Bold':       os.path.join(rl_fonts, 'VeraBd.ttf'),
            'Italic':     os.path.join(rl_fonts, 'VeraIt.ttf'),
            'BoldItalic': os.path.join(rl_fonts, 'VeraBI.ttf'),
        }
        if all(os.path.exists(v) for v in vera.values()):
            pdfmetrics.registerFont(TTFont('Roman', vera['Regular']))
            pdfmetrics.registerFont(TTFont('Roman-Bold', vera['Bold']))
            pdfmetrics.registerFont(TTFont('Roman-Italic', vera['Italic']))
            pdfmetrics.registerFont(TTFont('Roman-BoldItalic', vera['BoldItalic']))
            from reportlab.lib.fonts import addMapping
            addMapping('Roman', 0, 0, 'Roman')
            addMapping('Roman', 1, 0, 'Roman-Bold')
            addMapping('Roman', 0, 1, 'Roman-Italic')
            addMapping('Roman', 1, 1, 'Roman-BoldItalic')
            return 'Roman'
    except Exception:
        pass

    return 'Helvetica'


BASE_FONT   = setup_fonts()
BOLD_FONT   = BASE_FONT + '-Bold'   if BASE_FONT != 'Helvetica' else 'Helvetica-Bold'
ITALIC_FONT = BASE_FONT + '-Italic' if BASE_FONT != 'Helvetica' else 'Helvetica-Oblique'

# ── COLORS ──────────────────────────────────────────
INK         = colors.HexColor('#0a0a0a')
INK2        = colors.HexColor('#404040')
INK3        = colors.HexColor('#737373')
INK4        = colors.HexColor('#a3a3a3')
PAPER2      = colors.HexColor('#fafafa')
RULE        = colors.HexColor('#e5e5e5')
BLUE        = colors.HexColor('#2D91CE')
RED         = colors.HexColor('#ef4444')
RED_LIGHT   = colors.HexColor('#fef2f2')
AMBER       = colors.HexColor('#f59e0b')
AMBER_LIGHT = colors.HexColor('#fffbeb')
GREEN       = colors.HexColor('#22c55e')
GREEN_LIGHT = colors.HexColor('#f0fdf4')
WHITE       = colors.white

PAGE_W, PAGE_H = A4
MARGIN = 20*mm


def get_sev_colors(sev):
    if sev == 'critical': return RED, RED_LIGHT
    if sev == 'warning':  return AMBER, AMBER_LIGHT
    return GREEN, GREEN_LIGHT


def get_score_color(s):
    if s >= 80: return GREEN
    if s >= 50: return AMBER
    return RED


def get_sev_label(sev):
    if sev == 'critical': return 'CRITIC'
    if sev == 'warning':  return 'AVERTISMENT'
    return 'INFO'


def safe(text):
    """Escapeaza caracterele speciale HTML pentru ReportLab Paragraph."""
    if not text:
        return ''
    text = str(text)
    text = text.replace('&', '&amp;')
    text = text.replace('<', '&lt;')
    text = text.replace('>', '&gt;')
    return text


def s(name, **kw):
    """Shortcut pentru ParagraphStyle."""
    base = dict(fontName=BASE_FONT, fontSize=10, textColor=INK2, leading=15, spaceAfter=3)
    base.update(kw)
    return ParagraphStyle(name, **base)


class PageCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        self.meta = kwargs.pop('meta', {})
        canvas.Canvas.__init__(self, *args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        n = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self._draw_chrome(n)
            canvas.Canvas.showPage(self)
        canvas.Canvas.save(self)

    def _draw_chrome(self, total):
        self.saveState()
        # Header bar
        self.setFillColor(INK)
        self.rect(0, PAGE_H - 14*mm, PAGE_W, 14*mm, fill=1, stroke=0)
        self.setFillColor(WHITE)
        self.setFont(BOLD_FONT, 8)
        self.drawString(MARGIN, PAGE_H - 9*mm, 'INOVEX AUDIT')
        self.setFont(BASE_FONT, 8)
        self.setFillColor(colors.HexColor('#888888'))
        self.drawRightString(PAGE_W - MARGIN, PAGE_H - 9*mm, self.meta.get('url', '')[:60])
        # Footer bar
        self.setFillColor(colors.HexColor('#f5f5f5'))
        self.rect(0, 0, PAGE_W, 10*mm, fill=1, stroke=0)
        self.setFillColor(INK3)
        self.setFont(BASE_FONT, 7)
        self.drawString(MARGIN, 3.5*mm, 'Inovex Audit powered by AI  |  inovex.ro  |  contact@inovex.ro  |  0750 456 096')
        self.drawRightString(PAGE_W - MARGIN, 3.5*mm, f'Pagina {self._pageNumber} din {total}')
        self.restoreState()


def build_pdf(data, output_path):
    audit   = data['audit']
    issues  = data['issues']
    summary = data.get('summary', '')
    score   = audit.get('score_total', 0)
    url     = audit.get('url', '')

    doc = SimpleDocTemplate(
        output_path, pagesize=A4,
        leftMargin=MARGIN, rightMargin=MARGIN,
        topMargin=18*mm, bottomMargin=14*mm,
        title=f'Raport Audit - {url}',
        author='Inovex Audit',
    )

    story = []

    # ── TITLU ────────────────────────────────────────
    story.append(Spacer(1, 5*mm))
    story.append(Paragraph('RAPORT AUDIT', s('kicker', fontName=BOLD_FONT, fontSize=8, textColor=BLUE, leading=10, spaceAfter=6)))
    story.append(Paragraph(safe(url), s('h1', fontName=BOLD_FONT, fontSize=20, textColor=INK, leading=24, spaceAfter=4)))
    story.append(Paragraph(f'Generat la: {safe(audit.get("completed_at", "N/A"))}', s('sm', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11)))
    story.append(Paragraph(f'Email: {safe(audit.get("email", "N/A"))}', s('sm2', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11, spaceAfter=5)))
    story.append(HRFlowable(width='100%', thickness=1, color=RULE, spaceAfter=5))

    # Scor general
    sc = get_score_color(score)
    story.append(Table([[
        Paragraph('SCOR GENERAL', s('sl', fontName=BOLD_FONT, fontSize=8, textColor=INK3)),
        Paragraph(f'<font size="26" color="{sc.hexval()}"><b>{score}</b></font>/100',
                  s('sn', fontName=BOLD_FONT, fontSize=11, textColor=INK3, leading=28)),
    ]], colWidths=[80*mm, 80*mm], style=TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor('#fafafa')),
        ('BOX', (0,0), (-1,-1), 1, RULE),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 10),
        ('BOTTOMPADDING', (0,0), (-1,-1), 10),
        ('LEFTPADDING', (0,0), (-1,-1), 14),
    ])))
    story.append(Spacer(1, 4*mm))

    # Grid categorii
    cats = [
        ('Tehnic',    audit.get('score_technical', 0)),
        ('SEO',       audit.get('score_seo', 0)),
        ('Legal',     audit.get('score_legal', 0)),
        ('E-E-A-T',   audit.get('score_eeeat', 0)),
        ('Continut',  audit.get('score_content', 0)),
        ('UX',        audit.get('score_ux', 0)),
    ]
    row = []
    for name, sv in cats:
        sc2 = get_score_color(sv)
        row.append(Paragraph(
            f'<b>{safe(name)}</b><br/><font size="18" color="{sc2.hexval()}"><b>{sv}</b></font>',
            s('cc', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=12, alignment=TA_CENTER)
        ))
    story.append(Table([row], colWidths=[27*mm]*6, style=TableStyle([
        ('BOX', (0,0), (-1,-1), 1, RULE),
        ('INNERGRID', (0,0), (-1,-1), 1, RULE),
        ('TOPPADDING', (0,0), (-1,-1), 9),
        ('BOTTOMPADDING', (0,0), (-1,-1), 9),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ])))
    story.append(Spacer(1, 5*mm))
    story.append(HRFlowable(width='100%', thickness=1, color=RULE, spaceAfter=4))

    # ── REZUMAT AI ────────────────────────────────────
    if summary:
        story.append(Spacer(1, 3*mm))
        story.append(Paragraph('REZUMAT SI PLAN DE ACTIUNE', s('kk', fontName=BOLD_FONT, fontSize=8, textColor=BLUE, leading=10, spaceAfter=5)))
        story.append(Paragraph('Analiza generata pe baza problemelor identificate', s('ks', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11, spaceAfter=6)))
        for para in summary.split('\n\n'):
            para = para.strip()
            if para:
                story.append(Paragraph(safe(para), s('sb', fontName=BASE_FONT, fontSize=10, textColor=INK2, leading=15, spaceAfter=5)))
        story.append(Spacer(1, 3*mm))
        story.append(HRFlowable(width='100%', thickness=1, color=RULE, spaceAfter=4))

    # ── PROBLEME ──────────────────────────────────────
    cat_labels = {
        'technical': 'Tehnic si Viteza',
        'seo':       'SEO On-Page',
        'legal':     'Legal si GDPR',
        'eeeat':     'E-E-A-T',
        'content':   'Continut AI',
        'ux':        'UX si Design',
    }
    cat_order = ['technical', 'seo', 'legal', 'eeeat', 'content', 'ux']

    grouped = {}
    for iss in issues:
        grouped.setdefault(iss.get('category', 'technical'), []).append(iss)

    for cat_key in cat_order:
        if cat_key not in grouped:
            continue
        cat_issues = sorted(grouped[cat_key],
                            key=lambda x: {'critical': 0, 'warning': 1, 'info': 2}.get(x.get('severity', 'info'), 2))
        cat_score = audit.get(f'score_{cat_key}', 0)
        sc3 = get_score_color(cat_score)

        story.append(Spacer(1, 4*mm))

        # Header categorie
        hdr = Table([[
            Paragraph(f'<b>{safe(cat_labels.get(cat_key, cat_key)).upper()}</b>',
                      s('ch', fontName=BOLD_FONT, fontSize=10, textColor=INK)),
            Paragraph(f'<b><font color="{sc3.hexval()}">{cat_score}/100</font></b>',
                      s('cs', fontName=BOLD_FONT, fontSize=12, textColor=sc3, alignment=TA_RIGHT)),
        ]], colWidths=[120*mm, 42*mm])
        hdr.setStyle(TableStyle([
            ('TOPPADDING', (0,0), (-1,-1), 3),
            ('BOTTOMPADDING', (0,0), (-1,-1), 5),
            ('LINEBELOW', (0,0), (-1,0), 1.5, INK),
            ('VALIGN', (0,0), (-1,-1), 'BOTTOM'),
        ]))
        story.append(KeepTogether([hdr, Spacer(1, 3*mm)]))

        for iss in cat_issues:
            sev = iss.get('severity', 'info')
            dot_col, bg_col = get_sev_colors(sev)
            sev_lbl = get_sev_label(sev)

            content = []
            content.append(Paragraph(
                f'<font color="{dot_col.hexval()}"><b>{sev_lbl}</b></font>',
                s('sv', fontName=BOLD_FONT, fontSize=7, leading=9, spaceAfter=3)
            ))
            content.append(Paragraph(
                f'<b>{safe(iss.get("title", ""))}</b>',
                s('it', fontName=BOLD_FONT, fontSize=11, textColor=INK, leading=14, spaceAfter=4)
            ))
            if iss.get('description'):
                content.append(Paragraph(safe(iss['description']),
                                          s('id', fontName=BASE_FONT, fontSize=10, textColor=INK2, leading=15, spaceAfter=4)))
            if iss.get('suggestion'):
                content.append(Paragraph(
                    f'<b>Solutie:</b> {safe(iss["suggestion"])}',
                    s('if', fontName=ITALIC_FONT, fontSize=9, textColor=BLUE, leading=13, leftIndent=10, spaceAfter=3)
                ))
            if iss.get('affected_url'):
                content.append(Paragraph(
                    f'<font color="{INK4.hexval()}">URL: {safe(iss["affected_url"][:80])}</font>',
                    s('iu', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11)
                ))
            steps = iss.get('steps', [])
            if steps:
                content.append(Spacer(1, 2*mm))
                content.append(Paragraph('<b>Pasi de implementare:</b>',
                                          s('sh', fontName=BOLD_FONT, fontSize=9, textColor=INK2, leading=12, spaceAfter=3)))
                for i, step in enumerate(steps, 1):
                    content.append(Paragraph(f'{i}. {safe(step)}',
                                              s('sp', fontName=BASE_FONT, fontSize=9, textColor=INK2, leading=13, leftIndent=12)))

            tbl = Table([[content]], colWidths=[158*mm])
            tbl.setStyle(TableStyle([
                ('BACKGROUND', (0,0), (-1,-1), bg_col),
                ('LEFTPADDING', (0,0), (-1,-1), 12),
                ('RIGHTPADDING', (0,0), (-1,-1), 12),
                ('TOPPADDING', (0,0), (-1,-1), 10),
                ('BOTTOMPADDING', (0,0), (-1,-1), 10),
                ('LINEAFTER', (0,0), (0,-1), 4, dot_col),
                ('BOX', (0,0), (-1,-1), 0.5, RULE),
            ]))
            story.append(KeepTogether([tbl, Spacer(1, 4*mm)]))

    # ── CTA FINAL ─────────────────────────────────────
    story.append(PageBreak())
    story.append(Spacer(1, 20*mm))
    cta = Table([
        [Paragraph('<b>Vrei sa rezolvam noi toate problemele?</b>',
                   s('ct', fontName=BOLD_FONT, fontSize=16, textColor=WHITE, leading=20, spaceAfter=8))],
        [Paragraph('Echipa Inovex.ro implementeaza toate imbunatatirile in 5-7 zile lucratoare, cu garantie de satisfactie.',
                   s('cb', fontName=BASE_FONT, fontSize=10, textColor=colors.HexColor('#aaaaaa'), leading=15, spaceAfter=14))],
        [Table([[
            Paragraph('contact@inovex.ro', s('ce', fontName=BOLD_FONT, fontSize=11, textColor=BLUE)),
            Paragraph('0750 456 096',      s('cp', fontName=BOLD_FONT, fontSize=11, textColor=BLUE, alignment=TA_RIGHT)),
        ]], colWidths=[79*mm, 79*mm])],
    ], colWidths=[162*mm])
    cta.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), INK),
        ('TOPPADDING', (0,0), (0,0), 20),
        ('LEFTPADDING', (0,0), (-1,-1), 20),
        ('RIGHTPADDING', (0,0), (-1,-1), 20),
        ('TOPPADDING', (0,1), (-1,-1), 4),
        ('BOTTOMPADDING', (0,-1), (-1,-1), 20),
    ]))
    story.append(cta)

    def make_canvas(*args, **kwargs):
        return PageCanvas(*args, **kwargs, meta={'url': url})

    doc.build(story, canvasmaker=make_canvas)
    return True


if __name__ == '__main__':
    if len(sys.argv) < 3:
        print("Usage: python generate_pdf.py <data.json> <output.pdf>")
        sys.exit(1)
    with open(sys.argv[1], 'r', encoding='utf-8') as f:
        data = json.load(f)
    success = build_pdf(data, sys.argv[2])
    sys.exit(0 if success else 1)
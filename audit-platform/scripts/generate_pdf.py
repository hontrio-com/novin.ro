#!/usr/bin/env python3
"""
Inovex Audit PDF Generator v2 — CWV + Quick Wins + Format K
Usage: python generate_pdf.py <audit_json_file> <output_pdf_file>
"""
import sys, json, os
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

# ── FONTS ────────────────────────────────────────────────
def setup_fonts():
    script_dir = os.path.dirname(os.path.abspath(__file__))
    search_paths = [
        os.path.join(script_dir, 'fonts'),
        r'C:\Windows\Fonts',
        '/usr/share/fonts/truetype/dejavu',
        '/usr/share/fonts/dejavu',
        '/usr/local/share/fonts',
        '/Library/Fonts',
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
    try:
        import reportlab
        rl = os.path.join(os.path.dirname(reportlab.__file__), 'fonts')
        vera = {'Regular': 'Vera.ttf', 'Bold': 'VeraBd.ttf', 'Italic': 'VeraIt.ttf', 'BoldItalic': 'VeraBI.ttf'}
        vera = {k: os.path.join(rl, v) for k, v in vera.items()}
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

# ── COLORS ───────────────────────────────────────────────
INK          = colors.HexColor('#0a0a0a')
INK2         = colors.HexColor('#404040')
INK3         = colors.HexColor('#737373')
INK4         = colors.HexColor('#a3a3a3')
PAPER2       = colors.HexColor('#fafafa')
PAPER3       = colors.HexColor('#f5f5f5')
RULE         = colors.HexColor('#e5e5e5')
BLUE         = colors.HexColor('#2D91CE')
BLUE_LIGHT   = colors.HexColor('#eff8ff')
RED          = colors.HexColor('#ef4444')
RED_LIGHT    = colors.HexColor('#fef2f2')
AMBER        = colors.HexColor('#f59e0b')
AMBER_LIGHT  = colors.HexColor('#fffbeb')
GREEN        = colors.HexColor('#22c55e')
GREEN_DARK   = colors.HexColor('#15803d')
GREEN_LIGHT  = colors.HexColor('#f0fdf4')
GREEN_BORDER = colors.HexColor('#bbf7d0')
WHITE        = colors.white
YELLOW_BG    = colors.HexColor('#fefce8')
YELLOW_BD    = colors.HexColor('#fde68a')

PAGE_W, PAGE_H = A4
MARGIN  = 20 * mm
CONTENT = PAGE_W - 2 * MARGIN

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
    if not text: return ''
    return str(text).replace('&','&amp;').replace('<','&lt;').replace('>','&gt;')

def p(name, **kw):
    base = dict(fontName=BASE_FONT, fontSize=10, textColor=INK2, leading=15, spaceAfter=3)
    base.update(kw)
    return ParagraphStyle(name, **base)

def section_header(title, subtitle=None):
    """Returnează un bloc header de secțiune cu linie."""
    items = [
        Paragraph(safe(title).upper(),
                  p('sh_title', fontName=BOLD_FONT, fontSize=10, textColor=INK, leading=13, spaceAfter=2 if subtitle else 6)),
    ]
    if subtitle:
        items.append(Paragraph(safe(subtitle), p('sh_sub', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11, spaceAfter=6)))
    items.append(HRFlowable(width='100%', thickness=1, color=RULE, spaceAfter=5))
    return items

def cwv_class(val, metric):
    if val is None: return 'na'
    thresholds = {
        'lcp':  (2500, 4000),
        'cls':  (0.1,  0.25),
        'inp':  (200,  500),
        'fcp':  (1800, 3000),
        'ttfb': (800,  1800),
        'tbt':  (200,  600),
    }
    if metric not in thresholds: return 'na'
    good, poor = thresholds[metric]
    return 'good' if val < good else ('needs' if val < poor else 'poor')

def cwv_color(cls):
    return {
        'good':  (GREEN,  GREEN_LIGHT,  GREEN_DARK),
        'needs': (AMBER,  AMBER_LIGHT,  colors.HexColor('#a16207')),
        'poor':  (RED,    RED_LIGHT,    colors.HexColor('#b91c1c')),
        'na':    (INK4,   PAPER3,       INK3),
    }.get(cls, (INK4, PAPER3, INK3))

def cwv_label(cls):
    return {'good': 'Bun', 'needs': 'Mediu', 'poor': 'Slab', 'na': 'N/A'}.get(cls, 'N/A')


# ── PAGE CHROME ──────────────────────────────────────────
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
        self.setFillColor(INK)
        self.rect(0, PAGE_H - 14*mm, PAGE_W, 14*mm, fill=1, stroke=0)
        self.setFillColor(WHITE)
        self.setFont(BOLD_FONT, 8)
        self.drawString(MARGIN, PAGE_H - 9*mm, 'INOVEX AUDIT')
        self.setFont(BASE_FONT, 8)
        self.setFillColor(colors.HexColor('#888888'))
        self.drawRightString(PAGE_W - MARGIN, PAGE_H - 9*mm, self.meta.get('url', '')[:60])
        self.setFillColor(PAPER3)
        self.rect(0, 0, PAGE_W, 10*mm, fill=1, stroke=0)
        self.setFillColor(INK3)
        self.setFont(BASE_FONT, 7)
        self.drawString(MARGIN, 3.5*mm, 'Inovex Audit powered by AI  |  inovex.ro  |  0750 456 096')
        self.drawRightString(PAGE_W - MARGIN, 3.5*mm, f'Pagina {self._pageNumber} din {total}')
        self.restoreState()


# ── MAIN BUILD ───────────────────────────────────────────
def build_pdf(data, output_path):
    audit      = data.get('audit', {})
    issues     = data.get('issues', [])
    pages      = data.get('pages', [])
    quick_wins = data.get('quick_wins', [])
    cwv        = data.get('cwv', {})
    summary    = data.get('summary', '')
    url        = audit.get('url', '')
    score      = audit.get('score_total', 0)

    doc = SimpleDocTemplate(
        output_path, pagesize=A4,
        leftMargin=MARGIN, rightMargin=MARGIN,
        topMargin=18*mm, bottomMargin=14*mm,
    )

    story = []

    # ── COVER ────────────────────────────────────────────
    story.append(Spacer(1, 6*mm))
    story.append(Paragraph('RAPORT AUDIT WEBSITE',
                            p('kicker', fontName=BOLD_FONT, fontSize=8, textColor=BLUE, leading=10, spaceAfter=6)))
    story.append(Paragraph(safe(url),
                            p('h1', fontName=BOLD_FONT, fontSize=18, textColor=INK, leading=22, spaceAfter=4)))
    story.append(Paragraph(
        f'Generat: {safe(audit.get("completed_at","N/A"))}  ·  {safe(audit.get("email",""))}',
        p('meta', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11, spaceAfter=6)))
    story.append(HRFlowable(width='100%', thickness=1, color=RULE, spaceAfter=6))

    # Scor general + categorii
    sc = get_score_color(score)
    story.append(Table([[
        Paragraph('SCOR GENERAL', p('sl', fontName=BOLD_FONT, fontSize=8, textColor=INK3, leading=10)),
        Paragraph(f'<font size="28" color="{sc.hexval()}"><b>{score}</b></font>'
                  f'<font size="11" color="{INK3.hexval()}">/100</font>',
                  p('sn', fontName=BOLD_FONT, fontSize=11, leading=30)),
    ]], colWidths=[90*mm, 70*mm], style=TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), PAPER2),
        ('BOX', (0,0), (-1,-1), 1, RULE),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 11),
        ('BOTTOMPADDING', (0,0), (-1,-1), 11),
        ('LEFTPADDING', (0,0), (-1,-1), 14),
    ])))
    story.append(Spacer(1, 3*mm))

    cats = [
        ('Tehnic',   audit.get('score_technical', 0)),
        ('SEO',      audit.get('score_seo', 0)),
        ('Legal',    audit.get('score_legal', 0)),
        ('E-E-A-T',  audit.get('score_eeeat', 0)),
        ('Continut', audit.get('score_content', 0)),
        ('UX',       audit.get('score_ux', 0)),
    ]
    row = []
    for name, sv in cats:
        sc2 = get_score_color(sv)
        row.append(Paragraph(
            f'<b>{safe(name)}</b><br/><font size="17" color="{sc2.hexval()}"><b>{sv}</b></font>',
            p('cc', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=12, alignment=TA_CENTER)
        ))
    story.append(Table([row], colWidths=[CONTENT/6]*6, style=TableStyle([
        ('BOX', (0,0), (-1,-1), 1, RULE),
        ('INNERGRID', (0,0), (-1,-1), 1, RULE),
        ('TOPPADDING', (0,0), (-1,-1), 9),
        ('BOTTOMPADDING', (0,0), (-1,-1), 9),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ])))
    story.append(Spacer(1, 5*mm))

    # ── QUICK WINS ───────────────────────────────────────
    if quick_wins:
        story.append(Spacer(1, 3*mm))
        story.extend(section_header('Quick Wins — rezolva acum, impact imediat',
                                    'Top probleme cu efort mic si impact mare'))
        effort_labels = {1: 'Foarte usor', 2: 'Usor', 3: 'Mediu', 4: 'Dificil'}

        for i, qw in enumerate(quick_wins):
            sev = qw.get('severity', 'warning')
            dot_col, bg_col = get_sev_colors(sev)
            effort = qw.get('effort', 3)
            effort_lbl = effort_labels.get(effort, 'Mediu')
            effort_dots = ('●' * effort) + ('○' * (4 - effort))
            impact_tags = [t.strip() for t in qw.get('impact', '').split(',') if t.strip()]

            content = [
                Paragraph(
                    f'<font color="{dot_col.hexval()}"><b>#{i+1}  {get_sev_label(sev)}</b></font>',
                    p('qw_sev', fontName=BOLD_FONT, fontSize=7, leading=9, spaceAfter=3)
                ),
                Paragraph(
                    f'<b>{safe(qw.get("title",""))}</b>',
                    p('qw_t', fontName=BOLD_FONT, fontSize=11, textColor=INK, leading=14, spaceAfter=4)
                ),
                Paragraph(
                    safe(qw.get('description', '')[:180]),
                    p('qw_d', fontName=BASE_FONT, fontSize=9, textColor=INK2, leading=13, spaceAfter=5)
                ),
                Paragraph(
                    f'Efort: <b>{effort_dots} {safe(effort_lbl)}</b>'
                    + (f'  ·  Impact: <b>{safe(", ".join(impact_tags))}</b>' if impact_tags else ''),
                    p('qw_m', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11)
                ),
            ]
            tbl = Table([[content]], colWidths=[CONTENT])
            tbl.setStyle(TableStyle([
                ('BACKGROUND', (0,0), (-1,-1), bg_col),
                ('LEFTPADDING', (0,0), (-1,-1), 12),
                ('RIGHTPADDING', (0,0), (-1,-1), 12),
                ('TOPPADDING', (0,0), (-1,-1), 9),
                ('BOTTOMPADDING', (0,0), (-1,-1), 9),
                ('LINEAFTER', (0,0), (0,-1), 4, dot_col),
                ('BOX', (0,0), (-1,-1), 0.5, RULE),
            ]))
            story.append(KeepTogether([tbl, Spacer(1, 4*mm)]))

    # ── CORE WEB VITALS ──────────────────────────────────
    if any(v is not None for v in cwv.values()):
        story.append(PageBreak())
        story.extend(section_header('Performanta & Core Web Vitals',
                                    'Date Google PageSpeed Insights · Mobile'))

        # Scoruri mobile / desktop
        mob_score  = cwv.get('mobile_score')
        desk_score = cwv.get('desktop_score')
        if mob_score is not None or desk_score is not None:
            score_row = []
            for label, val in [('Mobile', mob_score), ('Desktop', desk_score)]:
                if val is not None:
                    sc_c = get_score_color(val)
                    score_row.append(Paragraph(
                        f'<b>{safe(label)}</b><br/>'
                        f'<font size="22" color="{sc_c.hexval()}"><b>{val}</b></font>'
                        f'<font size="9" color="{INK3.hexval()}">/100</font>',
                        p(f'ps_{label}', fontName=BASE_FONT, fontSize=9, textColor=INK3,
                          leading=14, alignment=TA_CENTER)
                    ))
                else:
                    score_row.append(Paragraph(''))
            pad = [Paragraph('')] * (2 - len(score_row))
            score_row = score_row + pad
            story.append(Table([score_row], colWidths=[CONTENT/2]*2, style=TableStyle([
                ('BOX', (0,0), (-1,-1), 1, RULE),
                ('INNERGRID', (0,0), (-1,-1), 1, RULE),
                ('TOPPADDING', (0,0), (-1,-1), 10),
                ('BOTTOMPADDING', (0,0), (-1,-1), 10),
                ('BACKGROUND', (0,0), (-1,-1), PAPER2),
                ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
            ])))
            story.append(Spacer(1, 4*mm))

        # Grid metrici CWV
        metrics = [
            ('LCP',  cwv.get('lcp_ms'),  cwv.get('lcp'),  'lcp',  True,  'Bun <2.5s'),
            ('CLS',  cwv.get('cls_raw'), cwv.get('cls'),  'cls',  True,  'Bun <0.1'),
            ('INP',  cwv.get('inp_ms'),  cwv.get('inp'),  'inp',  True,  'Bun <200ms'),
            ('FCP',  None,               cwv.get('fcp'),  'fcp',  False, 'Bun <1.8s'),
            ('TTFB', cwv.get('ttfb'),    cwv.get('ttfb'), 'ttfb', False, 'Bun <800ms'),
            ('TBT',  None,               cwv.get('tbt'),  'tbt',  False, 'Bun <200ms'),
        ]
        metric_cells = []
        for name, val_num, val_disp, key, is_core, thresh in metrics:
            cls = cwv_class(val_num, key)
            dot_c, bg_c, txt_c = cwv_color(cls)
            lbl = cwv_label(cls)
            core_txt = ' [CWV]' if is_core else ''
            cell = Paragraph(
                f'<b>{safe(name)}{safe(core_txt)}</b><br/>'
                f'<font color="{dot_c.hexval()}"><b>{safe(lbl)}</b></font><br/>'
                f'<font size="9" color="{INK3.hexval()}">{safe(val_disp or "N/A")}</font><br/>'
                f'<font size="7" color="{INK4.hexval()}">{safe(thresh)}</font>',
                p(f'cwv_{name}', fontName=BASE_FONT, fontSize=9, textColor=INK2,
                  leading=13, alignment=TA_CENTER)
            )
            metric_cells.append((cell, bg_c))

        # 3 coloane per rând
        for row_start in range(0, len(metric_cells), 3):
            chunk = metric_cells[row_start:row_start+3]
            while len(chunk) < 3:
                chunk.append((Paragraph(''), WHITE))
            row_data  = [[c[0] for c in chunk]]
            row_style = [
                ('BOX', (0,0), (-1,-1), 1, RULE),
                ('INNERGRID', (0,0), (-1,-1), 1, RULE),
                ('TOPPADDING', (0,0), (-1,-1), 10),
                ('BOTTOMPADDING', (0,0), (-1,-1), 10),
                ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
            ]
            for ci, (_, bg) in enumerate(chunk):
                row_style.append(('BACKGROUND', (ci,0), (ci,0), bg))
            story.append(Table(row_data, colWidths=[CONTENT/3]*3, style=TableStyle(row_style)))
            story.append(Spacer(1, 2*mm))
        story.append(Spacer(1, 3*mm))

    # ── PAGINI SCANATE ───────────────────────────────────
    if pages:
        story.append(Spacer(1, 3*mm))
        story.extend(section_header('Pagini scanate',
                                    f'{len(pages)} pagini analizate'))
        type_labels = {
            'home':'Home','contact':'Contact','about':'Despre',
            'services':'Servicii','blog':'Blog','category':'Categorie',
            'product':'Produs','checkout':'Checkout','faq':'FAQ',
            'legal':'Legal','other':'Alta pagina',
        }
        # Header tabel
        header = [
            Paragraph('<b>Pagina</b>',     p('th', fontName=BOLD_FONT, fontSize=8, textColor=INK3, leading=10)),
            Paragraph('<b>Tip</b>',        p('th', fontName=BOLD_FONT, fontSize=8, textColor=INK3, leading=10)),
            Paragraph('<b>Status</b>',     p('th', fontName=BOLD_FONT, fontSize=8, textColor=INK3, leading=10)),
            Paragraph('<b>Viteza</b>',     p('th', fontName=BOLD_FONT, fontSize=8, textColor=INK3, leading=10)),
            Paragraph('<b>Alt lipsa</b>',  p('th', fontName=BOLD_FONT, fontSize=8, textColor=INK3, leading=10)),
        ]
        rows = [header]
        for pg in pages[:15]:  # max 15 rânduri
            sc_val = pg.get('status_code', 0) or 0
            sc_col = GREEN if sc_val < 300 else (AMBER if sc_val < 400 else RED)
            lt     = pg.get('load_time_ms')
            lt_str = f'{round(lt/1000,2)}s' if lt and lt >= 1000 else (f'{lt}ms' if lt else 'N/A')
            lt_col = GREEN if (lt or 9999) < 1000 else (AMBER if (lt or 9999) < 2500 else RED)
            missing_alt = pg.get('images_missing_alt', 0)
            total_img   = pg.get('images_total', 0)
            url_short = pg.get('url', '')
            if len(url_short) > 45:
                url_short = '...' + url_short[-42:]
            row = [
                Paragraph(safe(url_short), p('td_url', fontName=BASE_FONT, fontSize=8, textColor=INK2, leading=11)),
                Paragraph(safe(type_labels.get(pg.get('page_type','other'), 'Alta pagina')),
                          p('td_t', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11)),
                Paragraph(f'<font color="{sc_col.hexval()}"><b>{sc_val or "N/A"}</b></font>',
                          p('td_s', fontName=BOLD_FONT, fontSize=9, leading=11, alignment=TA_CENTER)),
                Paragraph(f'<font color="{lt_col.hexval()}"><b>{safe(lt_str)}</b></font>',
                          p('td_l', fontName=BOLD_FONT, fontSize=9, leading=11, alignment=TA_CENTER)),
                Paragraph(
                    f'<font color="{(AMBER if missing_alt else GREEN).hexval()}"><b>{missing_alt}/{total_img}</b></font>',
                    p('td_a', fontName=BOLD_FONT, fontSize=9, leading=11, alignment=TA_CENTER)
                ),
            ]
            rows.append(row)

        tbl = Table(rows, colWidths=[65*mm, 28*mm, 18*mm, 20*mm, 20*mm])
        style = [
            ('BACKGROUND', (0,0), (-1,0), INK),
            ('TEXTCOLOR', (0,0), (-1,0), WHITE),
            ('BOX', (0,0), (-1,-1), 1, RULE),
            ('INNERGRID', (0,0), (-1,-1), 0.5, RULE),
            ('TOPPADDING', (0,0), (-1,-1), 6),
            ('BOTTOMPADDING', (0,0), (-1,-1), 6),
            ('LEFTPADDING', (0,0), (-1,-1), 8),
            ('RIGHTPADDING', (0,0), (-1,-1), 8),
            ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ]
        for ri in range(1, len(rows)):
            if ri % 2 == 0:
                style.append(('BACKGROUND', (0,ri), (-1,ri), PAPER2))
        tbl.setStyle(TableStyle(style))
        story.append(tbl)
        story.append(Spacer(1, 5*mm))

    # ── REZUMAT AI ───────────────────────────────────────
    if summary:
        story.append(PageBreak())
        story.extend(section_header('Rezumat AI si plan de actiune',
                                    'Analiza generata automat pe baza problemelor identificate'))
        for para in summary.split('\n\n'):
            para = para.strip()
            if para:
                story.append(Paragraph(safe(para),
                                        p('sb', fontName=BASE_FONT, fontSize=10, textColor=INK2,
                                          leading=15, spaceAfter=5)))
        story.append(Spacer(1, 3*mm))

    # ── PROBLEME PE CATEGORII (Format K) ─────────────────
    cat_labels = {
        'technical': 'Tehnic si Viteza',
        'seo':       'SEO On-Page',
        'legal':     'Legal si GDPR',
        'eeeat':     'E-E-A-T',
        'content':   'Continut AI',
        'ux':        'UX si Design',
    }
    cat_order = ['technical', 'seo', 'legal', 'eeeat', 'content', 'ux']
    impact_colors = {
        'SEO':      colors.HexColor('#dbeafe'),
        'UX':       colors.HexColor('#f3e8ff'),
        'Legal':    colors.HexColor('#fef9c3'),
        'Conversie':colors.HexColor('#dcfce7'),
        'Security': colors.HexColor('#fee2e2'),
    }
    impact_text_colors = {
        'SEO':      colors.HexColor('#1d4ed8'),
        'UX':       colors.HexColor('#7c3aed'),
        'Legal':    colors.HexColor('#a16207'),
        'Conversie':colors.HexColor('#15803d'),
        'Security': colors.HexColor('#b91c1c'),
    }

    grouped = {}
    for iss in issues:
        grouped.setdefault(iss.get('category', 'technical'), []).append(iss)

    for cat_key in cat_order:
        if cat_key not in grouped:
            continue
        cat_issues = sorted(grouped[cat_key],
                            key=lambda x: {'critical':0,'warning':1,'info':2}.get(x.get('severity','info'), 2))
        cat_score = audit.get(f'score_{cat_key}', 0)
        sc3 = get_score_color(cat_score)

        story.append(Spacer(1, 5*mm))

        # Header categorie
        hdr = Table([[
            Paragraph(f'<b>{safe(cat_labels.get(cat_key, cat_key)).upper()}</b>',
                      p('ch', fontName=BOLD_FONT, fontSize=10, textColor=INK)),
            Paragraph(f'<font color="{sc3.hexval()}"><b>{cat_score}/100</b></font>',
                      p('cs', fontName=BOLD_FONT, fontSize=12, alignment=TA_RIGHT)),
        ]], colWidths=[CONTENT*0.75, CONTENT*0.25])
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
            sev_lbl  = get_sev_label(sev)
            impacts  = [t.strip() for t in iss.get('impact', '').split(',') if t.strip()]
            time_est = {'critical': '30-120 min', 'warning': '15-60 min'}.get(sev, '5-20 min')

            content = []

            # Severity + impact tags + timp estimat
            meta_parts = [f'<font color="{dot_col.hexval()}"><b>{sev_lbl}</b></font>']
            for imp in impacts:
                tc = impact_text_colors.get(imp, INK3)
                meta_parts.append(f'  <font color="{tc.hexval()}"><b>[{safe(imp)}]</b></font>')
            meta_parts.append(f'  <font color="{INK4.hexval()}">~ {time_est}</font>')
            content.append(Paragraph(''.join(meta_parts),
                                      p('iss_meta', fontName=BOLD_FONT, fontSize=7, leading=10, spaceAfter=4)))

            # Titlu
            content.append(Paragraph(f'<b>{safe(iss.get("title",""))}</b>',
                                      p('iss_t', fontName=BOLD_FONT, fontSize=11, textColor=INK,
                                        leading=14, spaceAfter=4)))

            # Descriere
            if iss.get('description'):
                content.append(Paragraph(safe(iss['description']),
                                          p('iss_d', fontName=BASE_FONT, fontSize=9.5, textColor=INK2,
                                            leading=14, spaceAfter=5)))

            # Soluție / pași
            if iss.get('suggestion'):
                content.append(Paragraph(
                    f'<b>Solutie:</b> {safe(iss["suggestion"])}',
                    p('iss_fix', fontName=ITALIC_FONT, fontSize=9, textColor=BLUE,
                      leading=13, leftIndent=10, spaceAfter=3)
                ))

            # URL afectat
            if iss.get('affected_url'):
                content.append(Paragraph(
                    f'<font color="{INK4.hexval()}">URL: {safe(iss["affected_url"][:80])}</font>',
                    p('iss_url', fontName=BASE_FONT, fontSize=8, textColor=INK3, leading=11)
                ))

            tbl = Table([[content]], colWidths=[CONTENT])
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

    # ── CTA FINAL ────────────────────────────────────────
    story.append(PageBreak())
    story.append(Spacer(1, 20*mm))
    cta = Table([
        [Paragraph('<b>Vrei sa rezolvam noi toate problemele?</b>',
                   p('ct', fontName=BOLD_FONT, fontSize=16, textColor=WHITE, leading=20, spaceAfter=8))],
        [Paragraph('Echipa Inovex.ro implementeaza toate imbunatatirile in 5-7 zile lucratoare, cu garantie.',
                   p('cb', fontName=BASE_FONT, fontSize=10,
                     textColor=colors.HexColor('#aaaaaa'), leading=15, spaceAfter=14))],
        [Table([[
            Paragraph('contact@inovex.ro', p('ce', fontName=BOLD_FONT, fontSize=11, textColor=BLUE)),
            Paragraph('0750 456 096', p('cp', fontName=BOLD_FONT, fontSize=11, textColor=BLUE, alignment=TA_RIGHT)),
        ]], colWidths=[CONTENT/2]*2)],
    ], colWidths=[CONTENT])
    cta.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), INK),
        ('LEFTPADDING', (0,0), (-1,-1), 20),
        ('RIGHTPADDING', (0,0), (-1,-1), 20),
        ('TOPPADDING', (0,0), (0,0), 20),
        ('TOPPADDING', (0,1), (-1,-1), 4),
        ('BOTTOMPADDING', (0,-1), (-1,-1), 20),
    ]))
    story.append(cta)

    doc.build(story, canvasmaker=lambda *a, **kw: PageCanvas(*a, **kw, meta={'url': url}))
    return True


if __name__ == '__main__':
    if len(sys.argv) < 3:
        print("Usage: python generate_pdf.py <data.json> <output.pdf>")
        sys.exit(1)
    with open(sys.argv[1], 'r', encoding='utf-8') as f:
        data = json.load(f)
    success = build_pdf(data, sys.argv[2])
    sys.exit(0 if success else 1)
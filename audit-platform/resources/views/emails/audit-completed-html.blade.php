<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Raport Audit — {{ $audit->url }}</title>
<!--[if mso]>
<noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
<![endif]-->
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
a { color: inherit; text-decoration: none; }
img { border: 0; display: block; }
</style>
</head>
<body style="background:#f4f4f5; margin:0; padding:0;">

@php
$scoreColor = $score >= 80 ? '#16a34a' : ($score >= 50 ? '#d97706' : '#dc2626');
$scoreBg    = $score >= 80 ? '#dcfce7' : ($score >= 50 ? '#fef9c3' : '#fee2e2');
$scoreLabel = $score >= 80 ? 'Bun' : ($score >= 50 ? 'Necesită îmbunătățiri' : 'Critic — acțiune urgentă');
$scoreEmoji = $score >= 80 ? '✅' : ($score >= 50 ? '⚠️' : '🔴');
@endphp

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f4f5; padding: 32px 16px;">
  <tr>
    <td align="center">

      <!-- Wrapper -->
      <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%;">

        <!-- ── HEADER ───────────────────────────────── -->
        <tr>
          <td style="background:#0a0a0a; border-radius:16px 16px 0 0; padding: 28px 36px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
                  <p style="font-size:11px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#888; margin-bottom:6px;">Inovex Audit</p>
                  <p style="font-size:22px; font-weight:800; color:#ffffff; letter-spacing:-0.5px; line-height:1.2;">Raportul tău este gata</p>
                  <p style="font-size:13px; color:#666; margin-top:6px;">{{ $audit->url }}</p>
                </td>
                <td align="right" valign="middle" style="padding-left:16px;">
                  <!-- Score ring (tabel simulat) -->
                  <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td align="center" style="background:{{ $scoreBg }}; border-radius:50%; width:72px; height:72px; vertical-align:middle;">
                        <p style="font-size:26px; font-weight:800; color:{{ $scoreColor }}; line-height:1;">{{ $score }}</p>
                        <p style="font-size:10px; color:{{ $scoreColor }}; font-weight:600;">/100</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- ── SCORE BANNER ─────────────────────────── -->
        <tr>
          <td style="background:{{ $scoreBg }}; padding:14px 36px; border-left:4px solid {{ $scoreColor }};">
            <p style="font-size:13px; font-weight:700; color:{{ $scoreColor }};">
              {{ $scoreEmoji }} {{ $scoreLabel }}
              &nbsp;·&nbsp;
              <span style="font-weight:400; color:{{ $scoreColor }};">
                @if($critical > 0){{ $critical }} {{ $critical === 1 ? 'problemă critică' : 'probleme critice' }}@endif
                @if($critical > 0 && $warnings > 0) · @endif
                @if($warnings > 0){{ $warnings }} {{ $warnings === 1 ? 'avertisment' : 'avertismente' }}@endif
              </span>
            </p>
          </td>
        </tr>

        <!-- ── BODY ─────────────────────────────────── -->
        <tr>
          <td style="background:#ffffff; padding: 32px 36px;">

            <!-- Salut -->
            <p style="font-size:15px; color:#0a0a0a; font-weight:600; margin-bottom:8px;">Bună ziua,</p>
            <p style="font-size:14px; color:#525252; line-height:1.7; margin-bottom:24px;">
              Am finalizat analiza completă pentru <strong>{{ $audit->url }}</strong>.
              Mai jos găsești un sumar al rezultatelor — raportul complet cu toți pașii de rezolvare este disponibil online.
            </p>

            <!-- ── CATEGORY SCORES ─────────────────── -->
            <p style="font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#a3a3a3; margin-bottom:12px;">Scoruri pe categorii</p>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px; border:1px solid #e5e5e5; border-radius:10px; overflow:hidden;">
              <tr>
                @php $catIdx = 0; @endphp
                @foreach($categories as $label => $catScore)
                @php
                  $catColor = $catScore >= 80 ? '#16a34a' : ($catScore >= 50 ? '#d97706' : '#dc2626');
                  $catBg    = $catScore >= 80 ? '#dcfce7' : ($catScore >= 50 ? '#fef9c3' : '#fee2e2');
                  $catIdx++;
                  $isLast   = $catIdx === count($categories);
                @endphp
                <td align="center" style="padding:14px 8px; border-right:{{ $isLast ? '0' : '1px solid #e5e5e5' }}; background:#fafafa;">
                  <p style="font-size:10px; color:#737373; font-weight:500; margin-bottom:6px;">{{ $label }}</p>
                  <p style="font-size:20px; font-weight:800; color:{{ $catColor }}; letter-spacing:-1px;">{{ $catScore }}</p>
                </td>
                @endforeach
              </tr>
              @foreach($categories as $label => $catScore)
              @php
                $barW = max(2, $catScore);
                $barColor = $catScore >= 80 ? '#22c55e' : ($catScore >= 50 ? '#f59e0b' : '#ef4444');
              @endphp
              @endforeach
            </table>

            @if($mobilePerfScore !== null)
            <!-- ── PAGESPEED HIGHLIGHT ─────────────── -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px; background:#eff8ff; border:1px solid #bfdbfe; border-radius:10px;">
              <tr>
                <td style="padding:16px 20px;">
                  <p style="font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#1d4ed8; margin-bottom:8px;">📱 Performanță Mobile (PageSpeed)</p>
                  <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td>
                        @php $psColor = $mobilePerfScore >= 90 ? '#16a34a' : ($mobilePerfScore >= 50 ? '#d97706' : '#dc2626'); @endphp
                        <p style="font-size:28px; font-weight:800; color:{{ $psColor }}; letter-spacing:-1px; line-height:1;">{{ $mobilePerfScore }}<span style="font-size:14px; color:#737373; font-weight:400;">/100</span></p>
                        <p style="font-size:11px; color:#525252; margin-top:4px;">
                          @if($lcp)<strong>LCP:</strong> {{ $lcp }} &nbsp;@endif
                          @if($cls)<strong>CLS:</strong> {{ $cls }}@endif
                        </p>
                      </td>
                      <td align="right" valign="middle">
                        @if($mobilePerfScore < 90)
                        <p style="font-size:12px; color:#1d4ed8; font-weight:600; max-width:160px; text-align:right; line-height:1.5;">Viteza afectează direct poziția în Google și rata de conversie</p>
                        @else
                        <p style="font-size:12px; color:#15803d; font-weight:600; max-width:160px; text-align:right; line-height:1.5;">Performanță excelentă! Site-ul se încarcă rapid 🚀</p>
                        @endif
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
            @endif

            @if($quickWins->count() > 0)
            <!-- ── QUICK WINS ──────────────────────── -->
            <p style="font-size:11px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#a3a3a3; margin-bottom:12px;">⚡ Quick Wins — rezolvă acum</p>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
              @foreach($quickWins as $i => $qw)
              @php
                $qwIssue  = $qw['issue'];
                $qwSev    = $qwIssue->severity;
                $qwColor  = $qwSev === 'critical' ? '#dc2626' : '#d97706';
                $qwBg     = $qwSev === 'critical' ? '#fef2f2' : '#fffbeb';
                $qwBorder = $qwSev === 'critical' ? '#ef4444' : '#f59e0b';
                $qwLabel  = $qwSev === 'critical' ? 'CRITIC' : 'AVERTISMENT';
              @endphp
              <tr>
                <td style="padding-bottom:8px;">
                  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:{{ $qwBg }}; border:1px solid #e5e5e5; border-left:4px solid {{ $qwBorder }}; border-radius:8px;">
                    <tr>
                      <td style="padding:14px 16px;">
                        <p style="font-size:10px; font-weight:700; color:{{ $qwColor }}; letter-spacing:.3px; text-transform:uppercase; margin-bottom:4px;">#{{ $i+1 }} &nbsp;{{ $qwLabel }}</p>
                        <p style="font-size:13px; font-weight:700; color:#0a0a0a; margin-bottom:4px;">{{ $qwIssue->title }}</p>
                        <p style="font-size:12px; color:#525252; line-height:1.6;">{{ \Illuminate\Support\Str::limit($qwIssue->description, 120) }}</p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              @endforeach
            </table>
            @endif

            <!-- ── CTA ────────────────────────────── -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
              <tr>
                <td align="center">
                  <a href="{{ $reportUrl }}"
                     style="display:inline-block; background:#0a0a0a; color:#ffffff; font-size:14px; font-weight:700; padding:14px 32px; border-radius:8px; letter-spacing:-.2px; text-decoration:none;">
                    Vezi Raportul Complet →
                  </a>
                </td>
              </tr>
              <tr>
                <td align="center" style="padding-top:10px;">
                  <p style="font-size:11px; color:#a3a3a3;">Raportul este disponibil <strong>30 de zile</strong> · {{ $audit->completed_at ? $audit->completed_at->format('d.m.Y') : now()->format('d.m.Y') }}</p>
                </td>
              </tr>
            </table>

            <!-- Separator -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
              <tr><td style="border-top:1px solid #e5e5e5; height:1px;"></td></tr>
            </table>

            <!-- Ofertă implementare -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fafafa; border:1px solid #e5e5e5; border-radius:10px; margin-bottom:8px;">
              <tr>
                <td style="padding:20px 24px;">
                  <p style="font-size:14px; font-weight:700; color:#0a0a0a; margin-bottom:6px;">Vrei să rezolvăm noi toate problemele?</p>
                  <p style="font-size:13px; color:#525252; line-height:1.65; margin-bottom:14px;">
                    Echipa Inovex.ro implementează toate îmbunătățirile în <strong>5–7 zile lucrătoare</strong>, cu garanție de satisfacție. Prețuri transparente, fără surprize.
                  </p>
                  <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                      <td style="padding-right:12px;">
                        <a href="https://inovex.ro/contact"
                           style="display:inline-block; background:#2D91CE; color:#ffffff; font-size:13px; font-weight:700; padding:10px 20px; border-radius:7px; text-decoration:none;">
                          Contactează-ne
                        </a>
                      </td>
                      <td>
                        <a href="tel:+40750456096"
                           style="display:inline-block; border:1px solid #e5e5e5; background:#ffffff; color:#0a0a0a; font-size:13px; font-weight:600; padding:10px 20px; border-radius:7px; text-decoration:none;">
                          0750 456 096
                        </a>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- ── FOOTER ────────────────────────────────── -->
        <tr>
          <td style="background:#f4f4f5; padding:20px 36px; border-radius:0 0 16px 16px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
                  <p style="font-size:11px; color:#a3a3a3; line-height:1.7;">
                    <strong style="color:#737373;">Inovex.ro</strong> · contact@inovex.ro · 0750 456 096<br>
                    Ai primit acest email deoarece ai solicitat un audit pentru <strong>{{ $audit->url }}</strong>.<br>
                    <a href="{{ $reportUrl }}" style="color:#2D91CE;">Accesează raportul</a>
                  </p>
                </td>
                <td align="right" valign="top">
                  <p style="font-size:10px; color:#d4d4d4;">Inovex Audit v2</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

      </table>
      <!-- /Wrapper -->

    </td>
  </tr>
</table>

</body>
</html>
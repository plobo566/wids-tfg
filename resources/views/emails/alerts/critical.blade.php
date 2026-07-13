<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Alerta Crítica de Seguridad</title>
</head>

<body style="margin:0;padding:0;background:#f4f4f7;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7;padding:40px 0;">
<tr>
<td align="center">

<table width="650" cellpadding="0" cellspacing="0"
       style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,.08);">

    <!-- Header -->
    <tr>
        <td align="center"
            style="padding:35px;background:#111827;">

            <img src="{{ config('app.url') }}/images/logowids.svg"
                 width="70"
                 alt="WIDS">

            <h1 style="margin:20px 0 0;color:#ffffff;font-size:28px;">
                WIDS
            </h1>

            <p style="margin-top:8px;color:#9ca3af;font-size:14px;">
                Web Intrusion Detection System
            </p>

        </td>
    </tr>

    <!-- Alert -->
    <tr>
        <td style="padding:40px;">

            <div style="
                display:inline-block;
                background:#fee2e2;
                color:#b91c1c;
                padding:8px 18px;
                border-radius:30px;
                font-size:13px;
                font-weight:bold;
            ">
                🚨 ALERTA CRÍTICA
            </div>

            <h2 style="margin-top:25px;color:#111827;">
                Se detectó un ataque de severidad máxima.
            </h2>

            <p style="font-size:15px;color:#4b5563;line-height:1.7;">
                El sistema WIDS detectó una actividad potencialmente maliciosa que requiere atención inmediata.
            </p>

        </td>
    </tr>

    <!-- Information -->
    <tr>
        <td style="padding:0 40px 30px;">

            <table width="100%" cellpadding="12" cellspacing="0"
                   style="border:1px solid #e5e7eb;border-radius:8px;">

                <tr style="background:#f9fafb;">
                    <td width="180"><strong>Regla</strong></td>
                    <td>{{ class_basename($detection->rule_name) }}</td>
                </tr>

                <tr>
                    <td><strong>Entidad</strong></td>
                    <td>{{ $detection->entity_type }}</td>
                </tr>

                <tr style="background:#f9fafb;">
                    <td><strong>Valor</strong></td>
                    <td>{{ $detection->entity_value }}</td>
                </tr>

                <tr>
                    <td><strong>Fecha</strong></td>
                    <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                </tr>

            </table>

        </td>
    </tr>

    <!-- Technical -->
    <tr>
        <td style="padding:0 40px;">

            <h3 style="color:#111827;">
                Información técnica
            </h3>

            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="
                        background:#111827;
                        color:#f3f4f6;
                        border-left:5px solid #f59e0b;
                        padding:20px;
                        font-family:Consolas,Monaco,monospace;
                        font-size:13px;
                        line-height:1.5;
                        white-space:pre-wrap;
                    ">
{!! nl2br(e(json_encode(
$detection->details,
JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
))) !!}
                    </td>
                </tr>
            </table>

        </td>
    </tr>

    <!-- Button -->
    <tr>
        <td align="center" style="padding:45px;">

            <a href="{{ url('/admin/detections') }}"
               style="
               background:#f59e0b;
               color:#ffffff;
               text-decoration:none;
               padding:15px 35px;
               border-radius:6px;
               font-weight:bold;
               display:inline-block;
               ">
                Ver en el Panel de Control
            </a>

        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td align="center"
            style="padding:30px;background:#f9fafb;color:#6b7280;font-size:13px;">

            Este mensaje fue generado automáticamente por el sistema
            <strong>WIDS</strong>.

            <br><br>

            © {{ date('Y') }} WIDS

        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>
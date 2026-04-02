<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Reset Your Password — MDRRMO Naic</title>
</head>
<body style="margin:0;padding:0;background:#f0f3f9;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f3f9;padding:32px 0;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.10);">

      <!-- GOV BAR -->
      <tr>
        <td style="background:#111d35;padding:6px 32px;font-size:10px;color:#4a5a7a;letter-spacing:1px;text-transform:uppercase;">
          Republic of the Philippines &nbsp;·&nbsp; Province of Cavite &nbsp;·&nbsp; Municipality of Naic
        </td>
      </tr>

      <!-- BRAND HEADER -->
      <tr>
        <td style="background:#1a2a4a;padding:24px 32px 20px;border-bottom:3px solid #e6a817;">
          <table width="100%" cellpadding="0" cellspacing="0"><tr>
            <td>
              <p style="margin:0;font-size:10px;color:#8fa0c2;text-transform:uppercase;letter-spacing:1px;">Office of the Municipal DRRMO</p>
              <h1 style="margin:4px 0 2px;font-size:22px;font-weight:700;color:#ffffff;">MDRRMO – Naic, Cavite</h1>
              <p style="margin:0;font-size:11px;color:#6b7a99;">Municipal Disaster Risk Reduction and Management Office</p>
            </td>
            <td align="right" valign="middle">
              <span style="display:inline-block;background:#1b3f7a;color:#f5c518;font-size:10px;font-weight:700;padding:4px 10px;border-radius:4px;letter-spacing:.8px;border:1px solid #f5c518;">PASSWORD RESET</span>
            </td>
          </tr></table>
        </td>
      </tr>

      <!-- BODY -->
      <tr>
        <td style="background:#ffffff;padding:32px 32px 0;">

          <p style="margin:0 0 8px;font-size:18px;font-weight:700;color:#1a2a4a;">Password Reset Request 🔐</p>
          <p style="margin:0 0 24px;font-size:14px;color:#4a5a7a;line-height:1.6;">
            We received a request to reset the password for your MDRRMO RBI System account.
            Click the button below to set a new password.
          </p>

          <!-- System email reminder -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#f8f9fc;border:1px solid #dde3ef;border-left:4px solid #2e6ddd;border-radius:0 8px 8px 0;padding:14px 18px;">
                <p style="margin:0 0 4px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#6b7a99;">🔑 Your System Login Email</p>
                <p style="margin:0;font-size:15px;font-weight:700;color:#1a2a4a;font-family:'Courier New',monospace;letter-spacing:.5px;">{{ $user->email }}</p>
                <p style="margin:6px 0 0;font-size:11px;color:#6b7a99;">Use this email to log in after resetting your password.</p>
              </td>
            </tr>
          </table>

          <!-- Expiry warning -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#fff7ed;border:1px solid #fed7aa;border-left:4px solid #f97316;border-radius:0 8px 8px 0;padding:12px 16px;">
                <p style="margin:0;font-size:12px;color:#9a3412;">
                  ⏰ <strong>This link expires in {{ $expiry }}.</strong> If you did not request a password reset, ignore this email — your password will not change.
                </p>
              </td>
            </tr>
          </table>

          <!-- CTA Button -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
            <tr>
              <td align="center">
                <a href="{{ $resetUrl }}"
                   style="display:inline-block;background:#1b3f7a;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:16px 40px;border-radius:4px;letter-spacing:.5px;">
                  🔐 Reset My Password
                </a>
              </td>
            </tr>
          </table>

          <p style="margin:0 0 28px;font-size:11px;color:#6b7a99;text-align:center;">
            Or copy this link into your browser:<br/>
            <span style="font-size:11px;color:#2e6ddd;word-break:break-all;">{{ $resetUrl }}</span>
          </p>

        </td>
      </tr>

      <!-- DIVIDER -->
      <tr><td style="background:#ffffff;padding:0 32px;"><hr style="border:none;border-top:1px solid #dde3ef;margin:0;"/></td></tr>

      <!-- FOOTER NOTE -->
      <tr>
        <td style="background:#ffffff;padding:20px 32px 28px;">
          <p style="margin:0;font-size:12px;color:#6b7a99;line-height:1.6;">
            If you did not request a password reset, no action is required. Your account remains secure.
            This is an automated message — do not reply to this email.
          </p>
        </td>
      </tr>

      <!-- FOOTER BAR -->
      <tr>
        <td style="background:#111d35;padding:14px 32px;">
          <table width="100%" cellpadding="0" cellspacing="0"><tr>
            <td style="font-size:11px;color:#4a5a7a;">© {{ date('Y') }} <strong style="color:#8fa0c2;">MDRRMO Naic, Cavite</strong></td>
            <td align="right" style="font-size:11px;"><a href="https://facebook.com/naicmdrrmo" style="color:#3b82f6;text-decoration:none;">facebook.com/naicmdrrmo</a></td>
          </tr></table>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
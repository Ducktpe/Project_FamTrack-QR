<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>You're Invited — MDRRMO Naic System</title>
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
              <span style="display:inline-block;background:#7c3aed;color:#fff;font-size:10px;font-weight:700;padding:4px 10px;border-radius:4px;letter-spacing:.8px;">INVITATION</span>
            </td>
          </tr></table>
        </td>
      </tr>

      <!-- BODY -->
      <tr>
        <td style="background:#ffffff;padding:32px 32px 0;">

          <!-- Greeting -->
          <p style="margin:0 0 8px;font-size:18px;font-weight:700;color:#1a2a4a;">You've Been Invited! 🎉</p>
          <p style="margin:0 0 24px;font-size:14px;color:#4a5a7a;line-height:1.6;">
            The Super Administrator of the <strong>MDRRMO Naic RBI System</strong> has created an account for you
            with the role of <strong>{{ $roleLabel }}</strong>. Click the button below to set up your account.
          </p>

          <!-- Role Banner -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#f0f3f9;border-left:4px solid #2e6ddd;border-radius:0 8px 8px 0;padding:14px 18px;">
                <p style="margin:0;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7a99;">Your Assigned Role</p>
                <p style="margin:4px 0 0;font-size:20px;font-weight:700;color:#1a2a4a;">{{ $roleLabel }}</p>
              </td>
            </tr>
          </table>

          <!-- Login Email Notice -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#f8f9fc;border:1px solid #dde3ef;border-radius:8px;padding:16px 18px;">
                <p style="margin:0 0 6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#6b7a99;">
                  🔑 Your System Login Email
                </p>
                <p style="margin:0;font-size:16px;font-weight:700;color:#1a2a4a;font-family:'Courier New',monospace;background:#eef2ff;padding:6px 12px;border-radius:6px;display:inline-block;letter-spacing:1px;">
                  {{ $loginEmail }}
                </p>
                <p style="margin:8px 0 0;font-size:12px;color:#6b7a99;">
                  This is your official login email. Save it — you will use this to log in to the system.
                </p>
              </td>
            </tr>
          </table>

          <!-- Expiry Warning -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
              <td style="background:#fff7ed;border:1px solid #fed7aa;border-left:4px solid #f97316;border-radius:0 8px 8px 0;padding:12px 16px;">
                <p style="margin:0;font-size:12px;color:#9a3412;">
                  ⏰ <strong>This invite link expires in 24 hours</strong> — on
                  <strong>{{ $expiresAt->format('F j, Y \a\t h:i A') }}</strong>.
                  After that, contact the Super Administrator for a new invite.
                </p>
              </td>
            </tr>
          </table>

          <!-- Privileges -->
          <p style="margin:0 0 12px;font-size:13px;font-weight:700;color:#1a2a4a;text-transform:uppercase;letter-spacing:.8px;">✅ Your Account Privileges</p>
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            @foreach ($privileges as $privilege)
            <tr>
              <td style="padding:7px 0;border-bottom:1px solid #f0f3f9;">
                <table cellpadding="0" cellspacing="0"><tr>
                  <td style="width:22px;vertical-align:top;padding-top:4px;">
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22a86a;"></span>
                  </td>
                  <td style="font-size:13px;color:#1e2d4d;line-height:1.5;">{{ $privilege }}</td>
                </tr></table>
              </td>
            </tr>
            @endforeach
          </table>

          <!-- CTA -->
          <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
            <tr>
              <td align="center">
                <a href="{{ $setupUrl }}"
                   style="display:inline-block;background:#7c3aed;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:16px 40px;border-radius:8px;letter-spacing:.5px;">
                  ✅ Set Up My Account
                </a>
              </td>
            </tr>
          </table>

          <p style="margin:0 0 28px;font-size:11px;color:#6b7a99;text-align:center;">
            Or copy this link into your browser:<br/>
            <span style="font-size:11px;color:#2e6ddd;word-break:break-all;">{{ $setupUrl }}</span>
          </p>

        </td>
      </tr>

      <!-- DIVIDER -->
      <tr><td style="background:#ffffff;padding:0 32px;"><hr style="border:none;border-top:1px solid #dde3ef;margin:0;"/></td></tr>

      <!-- STEPS -->
      <tr>
        <td style="background:#ffffff;padding:20px 32px 28px;">
          <p style="margin:0 0 12px;font-size:12px;font-weight:700;color:#1a2a4a;text-transform:uppercase;letter-spacing:.8px;">📋 How to Get Started</p>
          <table width="100%" cellpadding="0" cellspacing="0">
            @foreach([
              '1' => 'Click the "Set Up My Account" button above.',
              '2' => 'Enter your full name and create a secure password.',
              '3' => 'Save your login email: ' . $loginEmail,
              '4' => 'Log in at the system using your login email and new password.',
            ] as $step => $text)
            <tr>
              <td style="padding:6px 0;border-bottom:1px solid #f0f3f9;">
                <table cellpadding="0" cellspacing="0"><tr>
                  <td style="width:28px;vertical-align:top;">
                    <span style="display:inline-block;width:20px;height:20px;border-radius:50%;background:#2e6ddd;color:#fff;font-size:11px;font-weight:700;text-align:center;line-height:20px;">{{ $step }}</span>
                  </td>
                  <td style="font-size:13px;color:#1e2d4d;line-height:1.5;padding-left:6px;">{{ $text }}</td>
                </tr></table>
              </td>
            </tr>
            @endforeach
          </table>
          <p style="margin:14px 0 0;font-size:12px;color:#6b7a99;line-height:1.6;">
            This is an automated message from the MDRRMO RBI System. If you did not expect this email, please ignore it or contact the Super Administrator.
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Password Reset</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: #f0eef8;
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 16px;
    }

    .email-wrapper {
      width: 100%;
      max-width: 560px;
      margin: 0 auto;
    }

    .preheader {
      font-size: 11px;
      color: #9b8ec4;
      text-align: center;
      letter-spacing: 0.06em;
      margin-bottom: 12px;
      font-family: 'Sora', sans-serif;
      text-transform: uppercase;
    }

    .email-card {
      background: #ffffff;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(107, 72, 212, 0.14), 0 2px 8px rgba(0,0,0,0.06);
    }

    /* ── Hero ── */
    .hero {
      background: linear-gradient(135deg, #7c3aed 0%, #9f5ffa 55%, #b97dff 100%);
      padding: 44px 36px 40px;
      position: relative;
      overflow: hidden;
      text-align: center;
    }
    .hero::before {
      content: '';
      position: absolute;
      width: 340px; height: 340px;
      border-radius: 50%;
      background: rgba(255,255,255,0.07);
      top: -120px; right: -80px;
    }
    .hero::after {
      content: '';
      position: absolute;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: rgba(255,255,255,0.05);
      bottom: -60px; left: -40px;
    }

    .hero-icon {
      position: relative;
      width: 64px; height: 64px;
      border-radius: 18px;
      background: rgba(255,255,255,0.18);
      border: 1.5px solid rgba(255,255,255,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      margin: 0 auto 20px;
    }

    .hero-title {
      font-family: 'Sora', sans-serif;
      font-size: 32px;
      font-weight: 800;
      color: #ffffff;
      line-height: 1.1;
      margin-bottom: 10px;
      position: relative;
    }

    .hero-subtitle {
      font-size: 14px;
      color: rgba(255,255,255,0.78);
      font-weight: 300;
      position: relative;
    }

    /* ── Body ── */
    .body-section {
      padding: 36px 40px 32px;
      text-align: center;
    }

    .greeting {
      font-family: 'Sora', sans-serif;
      font-size: 16px;
      font-weight: 600;
      color: #3b1fa8;
      margin-bottom: 8px;
    }

    .instruction {
      font-size: 14px;
      color: #6b5b95;
      line-height: 1.6;
      margin-bottom: 28px;
    }

    .otp-label {
      font-family: 'Sora', sans-serif;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: #9f5ffa;
      margin-bottom: 14px;
    }

    /* OTP box */
    .otp-box {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #f3eeff, #ede9fb);
      border: 2px solid #d8b4fe;
      border-radius: 18px;
      padding: 20px 40px;
      margin-bottom: 16px;
    }

    .otp-code {
      font-family: 'Sora', sans-serif;
      font-size: 16px;
      font-weight: 300;
      color: #6d28d9;
      letter-spacing: 0.11em;
      line-height: 1;
    }

    .expiry-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #fff1f2;
      border: 1px solid #fecdd3;
      border-radius: 100px;
      padding: 5px 14px;
      font-size: 12px;
      font-weight: 600;
      color: #e11d48;
      margin-bottom: 28px;
      font-family: 'Sora', sans-serif;
    }

    .divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, #e8e0fb 30%, #e8e0fb 70%, transparent);
      margin: 4px 0 24px;
    }

    .security-note {
      font-size: 12.5px;
      color: #a08fc5;
      line-height: 1.7;
      background: #fdfcff;
      border: 1.5px solid #ede9fb;
      border-radius: 12px;
      padding: 14px 18px;
      text-align: left;
    }
    .security-note strong {
      color: #6d28d9;
      font-weight: 600;
    }

    /* ── Footer ── */
    .email-footer {
      background: #f7f4fe;
      border-top: 1px solid #ede9fb;
      padding: 24px 36px;
      text-align: center;
    }
    .footer-logo {
      font-family: 'Sora', sans-serif;
      font-size: 12px;
      font-weight: 800;
      color: #7c3aed;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      margin-bottom: 12px;
    }
    .footer-links {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 12px;
      flex-wrap: wrap;
    }
    .footer-links a {
      font-size: 11.5px;
      color: #9b8ec4;
      text-decoration: none;
      font-weight: 500;
    }
    .footer-links a:hover { color: #7c3aed; }
    .footer-note {
      font-size: 10.5px;
      color: #c4b8e8;
      line-height: 1.6;
    }
  </style>
</head>
<body>

<div class="email-wrapper">
  <p class="preheader">Mobile Loan Application</p>

  <div class="email-card">

    <!-- Hero -->
    <div class="hero">
      <div class="hero-icon" style="align-items: center">🔐</div>
      <h1 class="hero-title">Password Reset</h1>
      <p class="hero-subtitle">Use the code below to reset your password. Kindly update your password after logging into the application</p>
    </div>

    <!-- Body -->
    <div class="body-section">

      <p class="greeting">Hello, {{ $data['name'] }} </p>
      <p class="instruction">
        We received a request to reset your password.<br/>
        Use the password characters below to proceed.
      </p>

      <div class="otp-label">Your One-Time Password</div>

      <div style="display:flex; justify-content:center; margin-bottom:16px;">
        <div class="otp-box">
          <span class="otp-code">{{ $data['password'] }}</span>
        </div>
      </div>

      <div style="display:flex; justify-content:center; margin-bottom:28px;">
        <div class="expiry-badge">
          ⏱ Expires in 5 minutes
        </div>
      </div>

      <div class="divider"></div>

      <div class="security-note">
        🔒 <strong>Security reminder:</strong> If you didn't request a password reset, please ignore this email or contact support immediately. <strong>Never share this code</strong> with anyone — our team will never ask for it.
      </div>

    </div>

    <!-- Footer -->
    <div class="email-footer">
      <div class="footer-logo">Mobile Loan Application</div>
      <div class="footer-links">
        <a href="#">Unsubscribe</a>
        <a href="#">Privacy Policy</a>
        <a href="#">Help Center</a>
        <a href="#">View Online</a>
      </div>
      <p class="footer-note">
        © 2026 Mobile Loan Application. All rights reserved.
      </p>
    </div>

  </div>
</div>

</body>
</html>

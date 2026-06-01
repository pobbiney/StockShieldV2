 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>OTP Preview</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
       <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
       <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f4f5fb;
        }

        /* LEFT PANEL */
        .left-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            background: #fff;
        }

        .login-form {
            width: 100%;
            max-width: 400px;
        }

        .form-header { text-align: center; margin-bottom: 32px; }

        .form-header h3 {
            font-size: 26px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #888;
            font-size: 14px;
            font-weight: 400;
        }

        .otp-label {
            display: block;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #444;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .otp-group {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-bottom: 32px;
        }

        .otp-input {
            width: 68px;
            height: 72px;
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            border: 2px solid #e2e4f0;
            border-radius: 16px;
            outline: none;
            background: #f8f9ff;
            color: #1a1a2e;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s, background 0.2s;
            caret-color: transparent;
            cursor: text;
        }

        .otp-input:focus {
            border-color: #5c6bc0;
            box-shadow: 0 0 0 4px rgba(92,107,192,0.13);
            background: #fff;
            transform: translateY(-3px);
        }

        .otp-input.filled {
            border-color: #5c6bc0;
            background: #eef0fb;
            color: #5c6bc0;
        }

        .btn-verify {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #5c6bc0, #3949ab);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.4px;
            transition: opacity 0.2s, transform 0.15s;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 20px;
        }

        .btn-verify:hover { opacity: 0.92; transform: translateY(-1px); }
        .btn-verify:active { transform: translateY(0); }

        .back-link {
            text-align: center;
            font-size: 13.5px;
            color: #888;
        }

        .back-link a { color: #5c6bc0; font-weight: 700; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }

        .resend {
            text-align: center;
            margin-bottom: 24px;
            font-size: 13px;
            color: #aaa;
        }

        .resend span { color: #5c6bc0; font-weight: 700; cursor: pointer; }
        .resend span:hover { text-decoration: underline; }

        /* RIGHT PANEL */
        .right-panel {
            flex: 1;
            background: linear-gradient(145deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(92,107,192,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .right-panel::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -40px;
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(255,100,130,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
            position: relative;
            z-index: 1;
        }

        .brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #5c6bc0, #e040fb);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.5px;
        }

        .illustration {
            width: 260px; height: 260px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 100px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 36px;
        }

        .right-quote {
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            font-weight: 400;
            text-align: center;
            max-width: 320px;
            line-height: 1.7;
            position: relative;
            z-index: 1;
            font-style: italic;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .otp-input { animation: fadeUp 0.4s ease both; }
        .otp-input:nth-child(1) { animation-delay: 0.1s; }
        .otp-input:nth-child(2) { animation-delay: 0.2s; }
        .otp-input:nth-child(3) { animation-delay: 0.3s; }
        .otp-input:nth-child(4) { animation-delay: 0.4s; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .right-panel { display: none; }
        }
    </style>
</head>
<body>
                             
    <!-- LEFT: Form -->
    <div class="left-panel">
          
        <div class="login-form">
             @if (session('message_success'))
								<p class="alert alert-success" align="center" style="color:green"><b>{{session('message_success')}}</b></p>
								@endif

								@if (session('message_error'))
								<p class="alert alert-danger" align="center" style="color: red">{{session('message_error')}}</p>
								@endif
            <div class="form-header">
                <h3>Provide OTP</h3>
                <p>Please enter the 4-digit code to reset your password</p>
            </div>
         
            <label class="otp-label">Type your 4-digit security code</label>
            <form enctype="multipart/form-data" method="POST" action="{{ route('reset-otp-process',$id) }}" >
                     @csrf
                <div class="otp-group" id="otpGroup">
                    <input class="otp-input" type="text" inputmode="numeric" maxlength="1" name="otp1" id="otp1" autocomplete="off" />
                    <input class="otp-input" type="text" inputmode="numeric" maxlength="1"  name="otp2" id="otp2" autocomplete="off" />
                    <input class="otp-input" type="text" inputmode="numeric" maxlength="1"  name="otp3" id="otp3" autocomplete="off" />
                    <input class="otp-input" type="text" inputmode="numeric" maxlength="1"   name="otp4" id="otp4" autocomplete="off" />
                </div>

              
               <button type="submit" class="btn-verify">Verify OTP</button>
               <input type="hidden" name="user_id" value="{{ $data->id }}"/>
            </form>
            <form enctype="multipart/form-data" action="{{ route('forgot-password-process') }}" method="POST">
                            @csrf
                <div class="resend">
                    Didn't receive the code?
                    <button type="submit" style="border:none;background:none;color:blue;cursor:pointer;">
                        Resend OTP
                    </button>
                </div>
                 <input type="hidden" name="email" value="{{ $data->email }}"/>
            </form>

            <div class="back-link">Back to <a href="/">Login</a></div>
        </div>
    </div>

    <!-- RIGHT: Branding -->
    <div class="right-panel">
        <div class="brand">
            <div class="brand-icon">💳</div>
            <span class="brand-name">Mobile Loan Application</span>
        </div>
        <div class="illustration">🔐</div>
        <p class="right-quote">"Your true value is determined by how much more you give in value than you take in payment."</p>
    </div>

<script>
    const inputs = document.querySelectorAll('.otp-input');

    inputs.forEach((input, i) => {
        input.addEventListener('input', (e) => {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val;
            if (val) {
                e.target.classList.add('filled');
                if (i < inputs.length - 1) inputs[i + 1].focus();
            } else {
                e.target.classList.remove('filled');
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && i > 0) {
                inputs[i - 1].focus();
                inputs[i - 1].value = '';
                inputs[i - 1].classList.remove('filled');
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 4);
            paste.split('').forEach((char, idx) => {
                if (inputs[idx]) { inputs[idx].value = char; inputs[idx].classList.add('filled'); }
            });
            if (inputs[paste.length - 1]) inputs[paste.length - 1].focus();
        });
    });
</script>

 <script>
 
@if(session('success_message'))
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: "{{ session('success_message') }}",
    showConfirmButton: false,
    timer: 2000
});
@endif
@if(session('error_message'))
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: "{{ session('error_message') }}",
     showConfirmButton: true,
    timer: 2000
});
@endif
</script>
</body>
</html>
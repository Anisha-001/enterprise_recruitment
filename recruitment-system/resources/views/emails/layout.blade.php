<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Application Update')</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background-color: #0f766e;
            padding: 30px 40px;
            text-align: left;
            border-bottom: 4px solid #0d9488;
        }
        .header .company-name {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.025em;
        }
        .header .company-tagline {
            color: #ccfbf1;
            font-size: 12px;
            margin: 4px 0 0 0;
            font-weight: 500;
        }
        .content {
            padding: 40px;
            color: #374151;
            line-height: 1.7;
            font-size: 16px;
        }
        .content h1 {
            color: #111827;
            font-size: 20px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 16px;
        }
        .button-wrapper {
            margin: 30px 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            background-color: #0f766e;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(15, 118, 110, 0.2);
            transition: background-color 0.2s;
        }
        .button:hover {
            background-color: #0d9488;
        }
        .details-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin: 24px 0;
        }
        .details-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-box td {
            padding: 6px 0;
            font-size: 14px;
            vertical-align: top;
        }
        .details-box td.label {
            color: #64748b;
            font-weight: 500;
            width: 35%;
        }
        .details-box td.value {
            color: #1e293b;
            font-weight: 600;
        }
        .footer {
            background-color: #f9fafb;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }
        .footer p {
            margin: 4px 0;
        }
        .footer a {
            color: #0f766e;
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 24px;
            }
            .header {
                padding: 24px;
            }
            .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="company-name">{{ config('recruitment.seo.company_name') }}</div>
                <div class="company-tagline">Careers & Candidate Portal</div>
            </div>
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                <p>This is an automated notification from {{ config('recruitment.seo.company_name') }} Careers Portal.</p>
                <p>&copy; {{ date('Y') }} {{ config('recruitment.seo.company_name') }}. All rights reserved.</p>
                <p style="font-size: 11px; margin-top: 12px; color: #9ca3af;">This email is confidential and intended solely for the addressee.</p>
            </div>
        </div>
    </div>
</body>
</html>

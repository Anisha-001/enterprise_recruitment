<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Offer Letter - {{ $offer->offer_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1f2937;
            padding: 50px;
        }
        .header {
            border-bottom: 3px solid #0f766e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: #0f766e;
        }
        .company-tagline {
            color: #6b7280;
            font-size: 10pt;
        }
        .document-title {
            font-size: 20pt;
            font-weight: bold;
            color: #111827;
            margin: 30px 0 10px;
        }
        .document-meta {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 30px;
        }
        .document-meta table {
            width: 100%;
        }
        .document-meta td {
            padding: 4px 0;
            font-size: 10pt;
        }
        .document-meta td:first-child {
            color: #6b7280;
            width: 40%;
        }
        .document-meta td:last-child {
            font-weight: 600;
            color: #111827;
        }
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0f766e;
            margin: 25px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .salary-table th {
            background: #f0fdfa;
            padding: 10px 15px;
            text-align: left;
            font-weight: 600;
            color: #0f766e;
            border-bottom: 2px solid #0f766e;
        }
        .salary-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .salary-table tr:last-child td {
            border-bottom: 2px solid #0f766e;
            font-weight: bold;
            color: #0f766e;
            font-size: 12pt;
        }
        .salary-table .amount {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #6b7280;
            text-align: center;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #374151;
            margin-top: 50px;
            padding-top: 8px;
            font-size: 10pt;
        }
        .acceptance-box {
            background: #f0fdfa;
            border: 2px solid #0f766e;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        .acceptance-box h3 {
            color: #0f766e;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ config('recruitment.seo.company_name') }}</div>
        <div class="company-tagline">Building the Future Together</div>
    </div>

    <div class="document-title">Offer of Employment</div>

    <div class="document-meta">
        <table>
            <tr>
                <td>Offer Number:</td>
                <td>{{ $offer->offer_number }}</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>{{ $offer->created_at->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>Candidate:</td>
                <td>{{ $offer->candidate->full_name }}</td>
            </tr>
            <tr>
                <td>Position:</td>
                <td>{{ $offer->proposed_designation }}</td>
            </tr>
            <tr>
                <td>Department:</td>
                <td>{{ $offer->department->name }}</td>
            </tr>
            <tr>
                <td>Valid Until:</td>
                <td>{{ $offer->offer_expiry_date->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    <p>Dear <strong>{{ $offer->candidate->full_name }}</strong>,</p>

    <p style="margin-top: 15px;">We are pleased to offer you the position of <strong>{{ $offer->proposed_designation }}</strong> with {{ config('recruitment.seo.company_name') }}. This offer is contingent upon the successful completion of background verification and reference checks.</p>

    <div class="section-title">Position Details</div>
    <p><strong>Job Title:</strong> {{ $offer->proposed_designation }}</p>
    <p><strong>Department:</strong> {{ $offer->department->name }}</p>
    <p><strong>Reporting To:</strong> {{ $offer->reportingManager?->full_name ?? 'To be assigned' }}</p>
    <p><strong>Location:</strong> {{ $offer->location->full_address }}</p>
    <p><strong>Employment Type:</strong> Full-Time</p>
    <p><strong>Proposed Joining Date:</strong> {{ $offer->joining_date->format('F d, Y') }}</p>

    <div class="section-title">Compensation & Benefits</div>
    <table class="salary-table">
        <thead>
            <tr>
                <th>Component</th>
                <th class="amount">Annual Amount ({{ $offer->salary_currency }})</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td class="amount">{{ number_format($offer->basic_salary, 2) }}</td>
            </tr>
            @if($offer->housing_allowance > 0)
            <tr>
                <td>Housing Allowance</td>
                <td class="amount">{{ number_format($offer->housing_allowance, 2) }}</td>
            </tr>
            @endif
            @if($offer->transport_allowance > 0)
            <tr>
                <td>Transport Allowance</td>
                <td class="amount">{{ number_format($offer->transport_allowance, 2) }}</td>
            </tr>
            @endif
            @if($offer->medical_allowance > 0)
            <tr>
                <td>Medical Allowance</td>
                <td class="amount">{{ number_format($offer->medical_allowance, 2) }}</td>
            </tr>
            @endif
            @if($offer->other_allowances > 0)
            <tr>
                <td>Other Allowances</td>
                <td class="amount">{{ number_format($offer->other_allowances, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td>Total Annual CTC</td>
                <td class="amount">{{ number_format($offer->total_ctc, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($offer->bonus_percentage)
    <p style="margin-top: 10px;"><strong>Performance Bonus:</strong> Up to {{ $offer->bonus_percentage }}% of annual basic salary, subject to company performance and individual achievement.</p>
    @endif

    @if($offer->special_conditions)
    <div class="section-title">Special Conditions</div>
    <p>{{ $offer->special_conditions }}</p>
    @endif

    <div class="section-title">Next Steps</div>
    <p>Please review this offer carefully. To accept this offer, please sign and return a copy of this letter by <strong>{{ $offer->offer_expiry_date->format('F d, Y') }}</strong>.</p>
    <p style="margin-top: 10px;">If you have any questions about this offer, please contact our HR team at <strong>{{ config('mail.from.address') }}</strong>.</p>

    <p style="margin-top: 20px;">We look forward to welcoming you to the team!</p>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong>For {{ config('recruitment.seo.company_name') }}</strong><br>
                Authorized Signatory
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>{{ $offer->candidate->full_name }}</strong><br>
                Candidate Signature & Date
            </div>
        </div>
    </div>

    <div class="acceptance-box">
        <h3>Acceptance of Offer</h3>
        <p>I, <strong>{{ $offer->candidate->full_name }}</strong>, accept the above offer of employment with {{ config('recruitment.seo.company_name') }} on the terms and conditions outlined in this letter.</p>
        <p style="margin-top: 15px;">Signature: _________________________ &nbsp;&nbsp; Date: _______________</p>
    </div>

    <div class="footer">
        <p>This document is confidential and intended solely for the named recipient.</p>
        <p>{{ config('recruitment.seo.company_name') }} &copy; {{ date('Y') }} - All Rights Reserved</p>
    </div>
</body>
</html>

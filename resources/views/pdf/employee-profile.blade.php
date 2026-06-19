<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* PDF specific resets */
        @page {
            margin: 40px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        /* Utility Classes */
        .full-width {
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .accent-color {
            color: #1a5c96;
        }

        /* Corporate Header */
        .header-table {
            border-bottom: 2px solid #1a5c96;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        /* Profile Section */
        .profile-container {
            margin-bottom: 20px;
        }

        .photo-container {
            width: 120px;
            /* Adjust size as needed */
            vertical-align: top;
        }

        .circle-wrapper {
            width: 120px;
            height: 120px;
            /* This creates the circle */
            border-radius: 50%;
            overflow: hidden;
            /* Optional: adds a nice frame border */
            border: 2px solid #1a5c96;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            /* 'object-fit' ensures the photo fills the circle without stretching */
            object-fit: cover;
        }

        .employee-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            color: #1a5c96;
        }

        .employee-title {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        /* Section Titles */
        .section-title {
            background-color: #f8f9fa;
            border-left: 4px solid #1a5c96;
            padding: 6px 10px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #1a5c96;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #f0f0f0;
        }

        .label {
            width: 30%;
            font-weight: bold;
            color: #555;
        }

        .value {
            width: 70%;
            color: #000;
        }

        /* For Employment grid (2 columns) */
        .info-grid {
            width: 100%;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px 0 0;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <table class="full-width header-table">
        <tr>
            <td>
                <img src="{{ public_path('img/ahg-logo-nobg.png') }}" height="60">
            </td>
            <td class="text-right">
                <h1 style="margin:0; color: #1a5c96;">EMPLOYEE DOSSIER</h1>
                <div class="text-muted" style="font-size: 9pt;">Confidential Report &bull; Report Generated:
                    {{ date('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- Top Profile Section -->
    <table class="full-width profile-container">
        <tr>
            <td class="photo-container">
                <div class="circle-wrapper">
                <img src="{{ public_path('img/default-photo.png') }}" class="profile-photo">
                </div>
            </td>
            <td style="padding-left: 25px; vertical-align: middle;">
                <div class="employee-name">{{ $employee->full_name }}</div>
                <div class="employee-title">{{ $employee->position }}</div>
                <div style="margin-top: 15px;">
                    <span style="color: #888;">Employee ID:</span>
                    <strong>{{ $employee->employee_id }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Content Wrapper -->
    <table class="info-grid">
        <tr>
            <!-- Left Column: Personal & Emergency -->
            <td>
                <div class="section-title">Personal Details</div>
                <table class="data-table">
                    <tr>
                        <td class="label">NRIC Number</td>
                        <td class="value">{{ $employee->ic_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Email Address</td>
                        <td class="value">{{ $employee->email }}</td>
                    </tr>
                    <tr>
                        <td class="label">Phone Number</td>
                        <td class="value">{{ $employee->phone_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Gender</td>
                        <td class="value">{{ ucfirst($employee->gender) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Birthday</td>
                        <td class="value">{{ $employee->birthday }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nationality</td>
                        <td class="value">{{ $employee->nationality }}</td>
                    </tr>
                    <tr>
                        <td class="label">Marital Status</td>
                        <td class="value">{{ $employee->marital_status }}</td>
                    </tr>
                </table>

                <div class="section-title">Emergency Contact</div>
                <table class="data-table">
                    <tr>
                        <td class="label">Name</td>
                        <td class="value">{{ $employee->emergency_contact_name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Relationship</td>
                        <td class="value">{{ $employee->emergency_contact_relationship }}</td>
                    </tr>
                    <tr>
                        <td class="label">Phone Number</td>
                        <td class="value">{{ $employee->emergency_contact_number }}</td>
                    </tr>
                </table>
            </td>

            <!-- Right Column: Education & Employment -->
            <td>
                <div class="section-title">Education</div>
                <table class="data-table">
                    <tr>
                        <td class="label">Highest Education Level</td>
                        <td class="value">{{ $employee->highest_education_level }}</td>
                    </tr>
                    <tr>
                        <td class="label">Highest Education Institution</td>
                        <td class="value">{{ $employee->highest_education_institution }}</td>
                    </tr>
                    <tr>
                        <td class="label">Graduation Year</td>
                        <td class="value">{{ $employee->graduation_year }}</td>
                    </tr>
                </table>

                <div class="section-title">Employment Overview</div>
                <table class="data-table">
                    <tr>
                        <td class="label">Department</td>
                        <td class="value">{{ $employee->employments->department->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Date of Employment</td>
                        <td class="value">
                            {{ $employee->date_of_employment ? $employee->date_of_employment->format('d M Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Employment Status</td>
                        <td class="value">{{ $employment->status?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Employment Type</td>
                        <td class="value">{{ $employment->type?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Company Branch</td>
                        <td class="value">{{ $employment->branch?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Report To</td>
                        <td class="value">{{ $employment->report_to ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Detailed Employment Records</div>
    <table class="data-table">
        <tr>
            <td class="label">Work Schedule</td>
            <td class="value">{{ $employment->work_start_time ?? '-' }} - {{ $employment->work_end_time ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">Probation Period</td>
            <td class="value">{{ $employment->probation_start ?? '-' }} to {{ $employment->probation_end ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">Resignation Date/Last Day</td>
            <td class="value" colspan="3">{{ $employment->resignation_date ?? '-' }}
                (Last Day:
                {{ $employment->last_working_day ?? '-' }})</td>
        </tr>

        <tr>
            <td class="label">Termination Date</td>
            <td class="value" colspan="3">{{ $employment->termination_date ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Suspension Period</td>
            <td class="value" colspan="3">{{ $employment->suspension_start ?? '-' }} to
                {{ $employment->suspension_end ?? '-' }}</td>
        </tr>
    </table>

    <!-- Footer -->
    <div style="position: fixed; bottom: -20px; left: 0; right: 0; text-align: center; color: #AAA; font-size: 10px;">
        Auto-generated confidential document. <br>
        &copy; {{ date('Y') }} Al-Hidayah Group HR Portal. All Rights Reserved.
    </div>

</body>

</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Magang')</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1f2937;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Card Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 32px 24px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">
                                Sistem Informasi Magang
                            </h1>
                            <p style="margin: 4px 0 0 0; color: #bfdbfe; font-size: 14px;">
                                Dinas Perpustakaan dan Kearsipan Provinsi Riau
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 32px 32px 32px; background-color: #ffffff;">
                            @yield('content')
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 32px; background-color: #f9fafb; border-top: 1px solid #f3f4f6; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #6b7280; line-height: 1.5;">
                                Email ini dikirim secara otomatis oleh Sistem Informasi Magang.<br>
                                Harap tidak membalas email ini secara langsung.
                            </p>
                            <p style="margin: 8px 0 0 0; font-size: 12px; color: #9ca3af; font-weight: 600;">
                                &copy; {{ date('Y') }} Dipusip Provinsi Riau. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Уведомление</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f7f7f7; font-family: Arial, sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f7f7f7;">
    <tr>
        <td align="center" style="padding: 20px 0;">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <tr>
                    <td style="background-color: #0d6efd; padding: 25px 30px; text-align: center;">
                        <h1 style="color: #ffffff; margin: 0; font-size: 22px;">Стоматологическая клиника</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 30px 30px 20px;">
                        <p style="font-size: 16px; color: #333;">Здравствуйте, <strong>{{ $userName }}</strong>!</p>
                        <p style="font-size: 16px; color: #333;">{{ $text }}</p>
                        <p style="font-size: 14px; color: #666; margin-top: 25px;">
                            Если у вас возникли вопросы, свяжитесь с нами любым удобным способом.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #f0f0f0; padding: 15px 30px; text-align: center; font-size: 12px; color: #888;">
                        С уважением, команда стоматологической клиники.<br>
                        © {{ date('Y') }} Все права защищены.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

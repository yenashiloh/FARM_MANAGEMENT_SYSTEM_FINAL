<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($announcement->subject); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body{margin-top:20px;}
</style>
<body>
    <table class="body-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;" bgcolor="#f6f6f6">
    <tbody>
        <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
            <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
            <td class="container" width="600" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;"
                valign="top">
                <div class="content" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                    <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;"
                        bgcolor="#fff">
                        <tbody>
                            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <td class="" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #fff; font-weight: 500; text-align: center; border-radius: 3px 3px 0 0; background-color: #810000; margin: 0; padding: 5px;"
                                    align="center" bgcolor="#71b6f9" valign="top">
                                     <p style="font-size:32px;color:#fff;">Announcement</p>
                                </td>
                            </tr>
                            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <td class="content-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tbody>
                                            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                </td>
                                            </tr>
                                            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
                                                    <?php echo $announcement->message; ?>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                   <div class="footer" 
                     style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; 
                            box-sizing: border-box; 
                            font-size: 14px; 
                            width: 100%; 
                            clear: both; 
                            color: #999; 
                            margin: 0; 
                            padding: 50px; 
                            text-align: center;">
                    <table width="100%" 
                           style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; 
                                  box-sizing: border-box; 
                                  font-size: 14px; 
                                  margin: 0; 
                                  text-align: center;">
                        <tbody>
                            <tr>
                                <td class="aligncenter content-block" 
                                    style="font-size: 12px; 
                                           vertical-align: top; 
                                           color: #999; 
                                           text-align: center; 
                                           margin: 0; 
                                           padding: 5px 0;">
                                    This is an automated email. Please do not reply directly to this message.
                                </td>
                            </tr>
                            <tr>
                                <td class="aligncenter content-block" 
                                    style="font-size: 12px; 
                                           vertical-align: top; 
                                           color: #999; 
                                           text-align: center; 
                                           margin: 0; 
                                           padding: 5px 0;">
                                   For document submissions, visit  
                                    <a href="https://pupt-farm.com/" 
                                       target="_blank" 
                                       style="color: #999; text-decoration: underline;">
                                        https://pupt-farm.com
                                    </a>.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
            </td>
            <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
        </tr>
    </tbody>
</table>
</body>
</html>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script><?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/admin/emails/announcement.blade.php ENDPATH**/ ?>
<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2024 May 27  Plugin version 2.0 $
 */

$define = [
    'HEADING_TITLE' => 'Export Email Addresses',

    'TEXT_EMAIL_EXPORT_FORMAT' => 'Export File Format:',
    'TEXT_PLEASE_SELECT_AUDIENCE' => 'Please choose the desired group of customers:',
    'TEXT_EMAIL_EXPORT_FILENAME' => 'Export Filename:',
    'ERROR_PLEASE_SELECT_AUDIENCE' => 'Error: Please select an audience list to export',
    'TEXT_PROCESSED' => 'Processed.',

    'TEXT_INSTRUCTIONS' => '
<u>INSTRUCTIONS</u><br>You can use this page to export your Zen Cart customers and/or newsletter subscribers list to a CSV or TXT file for easy import into an email program\'s address book.<br>
Thus, you can use a 3rd-party emailing tool for sending your advertising newsletters, etc.<br><br>
1. Choose your export format.<br>
2. Choose the snapshot of customer info (recipient list).<br>
3. Enter a filename.  Consider your choice of file extension (must end in one of: .csv .txt .htm .xml).<br>
&nbsp;&nbsp;&nbsp;&nbsp;If you use .TXT, you can save it or open it in a Text Editor directly.<br>
&nbsp;&nbsp;&nbsp;&nbsp;If you use .CSV, you can save it or open it in a spreadsheet program directly.<br>
&nbsp;&nbsp;&nbsp;&nbsp;If you use .XML, you can save it for import into another application.<br>
&nbsp;&nbsp;&nbsp;&nbsp;If you use .HTML the output will be shown directly on-screen, without downloading.<br>
4. Click Save to proceed.<br>
5. You may be prompted to save the downloaded file, or it may download automatically according to browser settings.'
];

return $define;

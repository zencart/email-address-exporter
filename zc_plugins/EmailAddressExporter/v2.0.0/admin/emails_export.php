<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2024 May 27  Plugin version 2.0 $
 *
 * @var messageStack $messageStack
 */

require 'includes/application_top.php';

$query_name = '';

$action = $_GET['action'] ?? '';
//if ($action === 'save') die(print_r($_POST, true));

$NL = "
"; // NOTE: The line break above is INTENTIONAL!

$available_export_formats[0] = ['id' => '0', 'text' => 'CSV'];
$available_export_formats[1] = ['id' => '1', 'text' => 'TXT'];
$available_export_formats[2] = ['id' => '2', 'text' => 'HTML'];
$available_export_formats[3] = ['id' => '3', 'text' => 'XML'];
$save_to_file_checked = !empty($_POST['savetofile']) ? $_POST['savetofile'] : 0;

$post_format = !empty($_POST['format']) ? $_POST['format'] : 1;
$format = $available_export_formats[$post_format]['text'];
$is_attachment = ($format === "CSV" || $format === "TXT" || $format === "XML");

$file = $_POST['filename'] ?? 'email_addresses.txt';
if (!preg_match('/.*\.(csv|txt|html?|xml)$/', $file)) {
    $file .= '.txt';
}

if (!empty($_POST['audience_selected'])) {
    $query_name = $_POST['audience_selected'];
    if (is_array($_POST['audience_selected'])) {
        $query_name = $_POST['audience_selected']['text'];
    }
}
if (empty($query_name)) {
    $messageStack->add(ERROR_PLEASE_SELECT_AUDIENCE, 'error');
    $action = '';
}


if ($action === 'save') {
    global $db;

    if ($format === 'CSV') {
        $FIELDSTART = '"';
        $FIELDEND = '"';
        $FIELDSEPARATOR = ',';
        $LINESTART = '';
        $LINEBREAK = "\n";
    }
    if ($format === 'TXT') {
        $FIELDSTART = '';
        $FIELDEND = '';
        $FIELDSEPARATOR = "\t";
        $LINESTART = '';
        $LINEBREAK = "\n";
    }
    if ($format === 'HTML') {
        $FIELDSTART = '<td>';
        $FIELDEND = '</td>';
        $FIELDSEPARATOR = "";
        $LINESTART = "<tr>";
        $LINEBREAK = "</tr>" . $NL;
    }

    /**
     * CUSTOMIZATION STEP 1:
     * 1. You must edit (or add to) the queries in the query_builder table if you want to add more fields to the extracted data.
     *    Look up your query_name (since this matches the pulldown in your admin), and update the query_string with the correct updated SQL query.
     *    Once the query in query_builder has been updated and tested, the following section of code will automatically
     *    bring in the right data for use in later steps.
     */
    $audience_select = get_audience_sql_query($query_name, 'newsletters');
    if (empty($audience_select['query_string'])) {
        $messageStack->add_session("No such query.", 'error');
        zen_redirect(zen_href_link(FILENAME_EMAIL_EXPORT));
    }

    zen_set_time_limit(600);

    $query_string = $audience_select['query_string'];
    $audience = $db->Execute($query_string);
    $records = $audience->RecordCount();
    $exporter_output = '';
    if ($records === 0) {
        $messageStack->add_session("No Records Found.", 'error');
    } else { //process records
        $i = 0;

        // make a <table> tag if HTML output
        if ($format === "HTML") {
            $exporter_output .= '<table border="1">' . $NL;
        }

        /**
         * CUSTOMIZATION STEP 2:
         * 2. You must add your field name to this list.
         *    Notice how head heading here involves two lines: FIELDSTART, then the heading, then FIELDEND, followed by line for the FIELDSEPARATOR if it's not the last field being output.
         *    Be sure to follow the same pattern.
         *    Best to only use letters/numbers and underscores. No other punctuation.
         */

        // add column headers if CSV or HTML format
        if ($format === "CSV" || $format === "HTML") {
            $exporter_output .= $LINESTART;
            $exporter_output .= $FIELDSTART . "customers_email_address" . $FIELDEND;
            $exporter_output .= $FIELDSEPARATOR;
            $exporter_output .= $FIELDSTART . "customers_firstname" . $FIELDEND;
            $exporter_output .= $FIELDSEPARATOR;
            $exporter_output .= $FIELDSTART . "customers_lastname" . $FIELDEND;
            $exporter_output .= $FIELDSEPARATOR;
            $exporter_output .= $FIELDSTART . "company_name" . $FIELDEND;
            $exporter_output .= $FIELDSEPARATOR;
            $exporter_output .= $FIELDSTART . "customers_telephone" . $FIELDEND;
            $exporter_output .= $LINEBREAK;
        }
        // headers - XML
        if ($format === "XML") {
            $exporter_output .= '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n";
        }

        // output real data
        foreach ($audience as $record) {
            $i++;

            /**
             * CUSTOMIZATION STEP 3:
             * 3. Add the new field to the output.
             *    The field's data is represented as: $record['FIELD_NAME_HERE'], as seen in the existing fields below.
             *    Be sure to add it for both the XML format and the non-XML format, for consistency.  Again, follow the pattern.
             */
            if ($format === "XML") {
                $exporter_output .= "<address_book>\n";
                $exporter_output .= "  <contact>\n";
                $exporter_output .= "    <firstname>" . $record['customers_firstname'] . "</firstname>\n";
                $exporter_output .= "    <lastname>" . $record['customers_lastname'] . "</lastname>\n";
                $exporter_output .= "    <email_address>" . $record['customers_email_address'] . "</email_address>\n";
                $exporter_output .= "    <company>" . ($record['entry_company'] ?? '') . "</company>\n";
                $exporter_output .= "    <telephone>" . ($record['customers_telephone'] ?? '') . "</telephone>\n";
                $exporter_output .= "  </contact>\n";
            } else {  // output non-XML data-format
                $exporter_output .= $LINESTART;
                $exporter_output .= $FIELDSTART . $record['customers_email_address'] . $FIELDEND;
                $exporter_output .= $FIELDSEPARATOR;
                $exporter_output .= $FIELDSTART . $record['customers_firstname'] . $FIELDEND;
                $exporter_output .= $FIELDSEPARATOR;
                $exporter_output .= $FIELDSTART . $record['customers_lastname'] . $FIELDEND;
                $exporter_output .= $FIELDSEPARATOR;
                $exporter_output .= $FIELDSTART . ($record['entry_company'] ?? '') . $FIELDEND;
                $exporter_output .= $FIELDSEPARATOR;
                $exporter_output .= $FIELDSTART . ($record['customers_telephone'] ?? '') . $FIELDEND;
                $exporter_output .= $LINEBREAK;
            }
        }

        if ($format === "HTML") {
            $exporter_output .= $NL . "</table>";
        }
        if ($format === "XML") {
            $exporter_output .= "</address_book>\n";
        }


        // theoretically, $i should === $records at this point.

        // status message
        $messageStack->add($records . ' ' . TEXT_PROCESSED, 'success');

        // begin streaming file contents
        if ($is_attachment) {
            if ($format === "CSV" || $format === "TXT") {
                $content_type = 'text/x-csv';
            } elseif ($format === "XML") {
                $content_type = 'text/xml; charset=' . CHARSET;
            }
            if (str_contains($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
                header('Content-Type: application/octetstream');
//              header('Content-Type: '.$content_type);
//              header('Content-Disposition: inline; filename="' . $file . '"');
                header('Content-Disposition: attachment; filename=' . $file);
                header("Expires: Mon, 26 Jul 2001 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: must_revalidate, post-check=0, pre-check=0");
                header("Pragma: public");
                header("Cache-control: private");
            } else {
                header('Content-Type: application/x-octet-stream');
//              header('Content-Type: '.$content_type);
                header('Content-Disposition: attachment; filename=' . $file);
                header("Expires: Mon, 26 Jul 2001 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Pragma: no-cache");
            }
            echo $exporter_output;
            session_write_close();
            exit;
        }

        echo $exporter_output;
        session_write_close();
        exit;
    } //end if $records for processing not 0

    zen_redirect(zen_href_link(FILENAME_EMAIL_EXPORT));
}

?>
<!doctype html>
<html <?= HTML_PARAMS ?>>
<head>
    <?php
    require DIR_WS_INCLUDES . 'admin_html_head.php';
    ?>
</head>
<body>
<!-- header //-->
<?php
require DIR_WS_INCLUDES . 'header.php';
?>
<!-- header_eof //-->
<div class="container-fluid">
    <!-- body //-->
    <h1><?= HEADING_TITLE ?></h1>
    <!-- body_text //-->
    <div class="row">
        <?= zen_draw_form('export', FILENAME_EMAIL_EXPORT, 'action=save', 'post', 'class="form-horizontal"') // 'onsubmit="return check_form(export);"');      ?>

        <div class="col-xs-12"><?= TEXT_INSTRUCTIONS ?></div>

        <div class="form-group"><?= zen_draw_label(TEXT_EMAIL_EXPORT_FORMAT, 'format', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <?= zen_draw_pull_down_menu('format', $available_export_formats, $post_format, 'class="form-control" id="format"') ?>
            </div>
        </div>

        <div class="form-group"><?= zen_draw_label(TEXT_PLEASE_SELECT_AUDIENCE, 'filter', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <?= zen_draw_pull_down_menu('audience_selected', get_audiences_list('newsletters'), $query_name, 'class="form-control" id="filter"') ?>
            </div>
        </div>

        <div class="form-group">
            <?= zen_draw_label(TEXT_EMAIL_EXPORT_FILENAME, 'filename', 'class="col-sm-3 control-label"') ?>
            <div class="col-sm-9 col-md-6">
                <?= zen_draw_input_field('filename', htmlspecialchars($file, ENT_COMPAT, CHARSET, true), 'class="form-control" size="60" id="filename"') ?>
            </div>
        </div>

         <div class="text-right">
            <button class="btn btn-primary"><?= IMAGE_GO ?></button>
            <a href="<?= zen_href_link(FILENAME_DEFAULT) ?>" class="btn btn-default" role="button"><?= IMAGE_CANCEL ?></a>
        </div>

        <?= '</form>' ?>
    </div>
    <!-- body_text_eof //-->

    <!-- body_eof //-->
</div>
<!-- footer //-->
<?php
require DIR_WS_INCLUDES . 'footer.php';
?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php
require DIR_WS_INCLUDES . 'application_bottom.php';
?>

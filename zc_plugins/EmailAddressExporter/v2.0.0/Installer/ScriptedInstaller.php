<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2024 May 27  Plugin version 2.0 $
 */

use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall(): void
    {
        // de-register first because filename changed from pre-encapsulated version
        zen_deregister_admin_pages(['emailExport']);
        zen_register_admin_page('emailExport', 'BOX_TOOLS_EMAIL_EXPORT', 'FILENAME_EMAIL_EXPORT', '', 'tools', 'Y', 10);

        // other tweaking to add slightly more flexibility when exporting email addresses
        $result = $this->dbConn->Execute("SELECT query_string FROM " . TABLE_QUERY_BUILDER . " WHERE query_name = 'All Customers'");
        if ($result->fields['query_string'] === 'SELECT customers_email_address, customers_firstname, customers_lastname FROM TABLE_CUSTOMERS ORDER BY customers_lastname, customers_firstname, customers_email_address') {
            $this->dbConn->Execute(
                "UPDATE " . TABLE_QUERY_BUILDER . " SET query_string='select customers_firstname, customers_lastname, customers_email_address, c.*, a.* from TABLE_CUSTOMERS c, TABLE_ADDRESS_BOOK a WHERE c.customers_id = a.customers_id AND c.customers_default_address_id = a.address_book_id ORDER BY customers_lastname, customers_firstname, customers_email_address'
                WHERE query_name = 'All Customers'"
            );
        }
        $result = $this->dbConn->Execute("SELECT query_string FROM " . TABLE_QUERY_BUILDER . " WHERE query_name = 'All Newsletter Subscribers'");
        if ($result->fields['query_string'] === "SELECT customers_firstname, customers_lastname, customers_email_address FROM TABLE_CUSTOMERS WHERE customers_newsletter = '1'") {
            $this->dbConn->Execute(
                "UPDATE " . TABLE_QUERY_BUILDER . " SET query_string='select customers_firstname, customers_lastname, customers_email_address, c.*, a.* from TABLE_CUSTOMERS c, TABLE_ADDRESS_BOOK a WHERE c.customers_id = a.customers_id AND c.customers_default_address_id = a.address_book_id AND customers_newsletter = 1'
                WHERE query_name = 'All Newsletter Subscribers'"
            );
        }
    }

    protected function executeUninstall(): void
    {
        zen_deregister_admin_pages(['emailExport']);
    }
}

# Email Address Exporter plugin for Zen Cart 

Add-On: Email Address Exporter Admin Tool
Designed for: Zen Cart v1.5.8+ 

Created by: DrByte
Some revisions added by: Swifty8078, Ooba_Scott, That Software Guy 

Donations:  Please support Zen Cart!  paypal@zen-cart.com  - Thank you!
===========================================================

NOTES:
This add-on was created to allow quick and easy exporting of email addresses from your Zen Cart shop.

This is especially useful if you are needing to manage mailing lists and/or send newsletters or mass-emailings from your personal computer.
One great tool to use this with is GroupMail, which you can get a free copy of here: https://www.shareasale.com/r.cfm?B=3937&U=151719&M=1465

As to export formats, you can choose from CSV, TXT (tab-delimited), XML, or HTML formats.

You can select any of the audiences available in your cart, using the pulldown menu provided.
The most common audience would be Newsletter recipients.

===========================================================

INSTALLATION:  
Upload all files from the zc_plugins directory to the zc_plugins directory on your server, retaining folder structures.

In your Admin permissions panel, grant access to appropriate users using the Admin Access Management screen.

===========================================================

USE:
To use, just log into the admin area, click on "Tools", and click on "Export Email Addresses".

===========================================================

HISTORY:
March 10/05 - Initial Release
April 15/05 - Added XML format
May 11/05   - Added Save To File format, and tweaked streaming methods slightly
July 2012 - updated for Zen Cart v1.5.0
Sept 2012 - DrByte updated for Zen Cart v1.5.1, and to add ability to easily add more fields to the output, along with THREE-STEPS INSTRUCTIONS in the code on how to do it.
Nov 2013 - DrByte added a fix to prevent a database error from occurring when you forget to choose an export option
Dec 2013 - DrByte removed debug code left in previous update.
Dec 2014 - tidying
Aug 2017 - included telephone and company fields in export (requires them to be in the db query that feeds the exporter though). Also forced limits on filenames used for exporting.
misc updates
May 2024 - converted to encapsulated plugin (also removed Save To File On Server feature, since most people simply download directly anyway).

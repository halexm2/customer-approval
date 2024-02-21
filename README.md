# Magento 2 Customer Approval Extension

## Requirements
* Magento 2.0.0-2.4.x

## Overview
Let your customers access the website only after your approval.

## Main Features
* Custom customer account creation success message
* Custom login attempt error message
* Email notification for the customer when the account is approved
* Rich configuration
* Additional pending customer's custom listing with mass actions
* Customer approval state can be seen on the customer listing page
* Mass actions for customer listing
* Pending customers badge in Admin Panel
* ACL added for customer approve and un-approve actions

## Installation
```bash
composer require halexm2/customer-approval
bin/magento setup:upgrade
```

## Screenshots
### Registration Message
![Registration Message](docs/img/registration_message.png)

### Login Attempt Message
![Login Attempt Message](docs/img/login_error_message.png)

### Approved Email Template
![Approved Email Template](docs/img/approved_email_template.png)

### Customer Edit Approve Action
![Customer Edit Approve Action](docs/img/customer_edit_form_button.png)

### Customer Grid Mass Action
![Customer Grid Mass Action](docs/img/customer_grid_massaction.png)

### Customer Grid Approval State Column
![Customer Grid Approval State Column](docs/img/customers_grid.png)

### Pending Customers Grid
![Customer Grid Approval State Column](docs/img/pending_customers_grid.png)

### Admin menu Pending Customers Badge
![Admin Menu Pending Customers Badge](docs/img/admin_menu_badge.png)

### Access Control List configuration
![ACL](docs/img/customer_approval_acl.png)
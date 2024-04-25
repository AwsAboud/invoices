# Invoices Management System

The Invoices Management System (IMS) is a comprehensive solution designed to streamline and automate the invoice processing workflow for businesses. By centralizing invoice generation, payments, and reconciliation, IMS enhances efficiency and reduces errors, ultimately saving time and resources for organizations.

## Key Features
- **Automated Invoice Processing:** IMS automates the generation and management of invoices, eliminating manual entry and reducing errors

- **Payment Tracking:** Track payments associated with invoices, providing visibility into outstanding balances and improving cash flow management.
- **Reconciliation:** Seamlessly reconcile invoices with payments, ensuring accurate financial reporting and minimizing discrepancies.

## Installation

1. **Clone the Repository**:
   
   ```bash
   git clone https://github.com/AwsAboud/invoices.git

2. **Install Dependencies**:
   
    ```bash
   comoposer install
    
3. **Set up environment**:
- Copy `.env.example` to `.env` and configure your database settings.

- Generate application key:
     ```bash
      php artisan key:generate

4. **Migrate Database**:
   
   ```bash
   php artisan migrate

5. **Start Server**:
   
   ```bash
   php artisan serve

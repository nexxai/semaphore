# Semaphore

A streamlined task management app designed for families with centralized coordination. Semaphore works best when one family member naturally takes the lead on household organization and planning, while other members focus on execution.

## How It Works

Semaphore recognizes that many families operate most efficiently with a designated coordinator who has visibility into the full scope of household needs, schedules, and priorities. This person can create and assign tasks, and track progress, while other family members receive clear, actionable assignments without needing to manage the broader organizational overhead.

Whether you're coordinating chores, managing family schedules, planning events, or organizing household projects, Semaphore provides a clear communication channel that reduces decision fatigue and eliminates the back-and-forth of "what should I do next?"

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & npm
- Database (SQLite by default, or MySQL/PostgreSQL)
- AWS account for email sending via SES

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd semaphore
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install JavaScript dependencies

```bash
npm install
```

### 4. Set up environment variables

```bash
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file to configure your database and other settings, ensuring you configure an `ADMIN_EMAIL` which is who will receive notifications for added tasks

### 5. Run database migrations

```bash
php artisan migrate
```

### 6. Build frontend assets

```bash
npm run build
```

### 7. Start the development server and required services

```bash
# Start the Laravel server, queue worker, and Vite development server
composer dev

# In a separate terminal, start the Reverb WebSocket server
php artisan reverb:start
```

The application will be available at `http://localhost:8000`.

**Note:** Both the queue worker and Reverb server need to be running for the application to function properly. The queue worker processes email notifications, and Reverb enables real-time updates in the UI.

## Security Considerations

**Important:** Semaphore does not include any built-in authentication or login protection. All application features and data are publicly accessible to anyone who can access the URL where the application is hosted.

If you plan to host Semaphore outside of a walled garden network (such as a private home network), you should implement additional security measures to protect your application and data. Some options include:

- Using [Cloudflare Access](https://www.cloudflare.com/products/zero-trust/access/) to add authentication in front of your application
- Setting up a VPN for secure remote access
- Implementing HTTP Basic Authentication at the web server level
- Using a reverse proxy with authentication capabilities

Failure to secure your application could result in unauthorized access to your family's task data and potential misuse of the application.

## Email Configuration

Semaphore recommends using AWS Simple Email Service (SES) for sending emails. Follow these steps to set up email sending:

### 1. Create an AWS Account

If you don't already have an AWS account, sign up at [aws.amazon.com](https://aws.amazon.com/).

### 2. Set up an IAM User

1. Go to the IAM console in AWS
2. Create a new user with programmatic access
3. Attach the `AmazonSESFullAccess` policy to the user
4. Save the Access Key ID and Secret Access Key

### 3. Verify Email Identities in SES

1. Go to the SES console in AWS
2. Navigate to "Verified identities"
3. Click "Create identity"
4. Choose "Email address" and enter the email address you want to send from
5. Follow the verification process by clicking the link in the verification email

### 4. Configure Semaphore to use SES

Update your `.env` file with the following settings:

```
MAIL_MAILER=ses
MAIL_FROM_ADDRESS=your-verified-email@example.com
MAIL_FROM_NAME=Semaphore

AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_DEFAULT_REGION=your-aws-region (e.g., us-east-1)
```

### 5. Moving out of the SES Sandbox

By default, new AWS accounts have SES in sandbox mode, which restricts you to:

- Only sending to verified email addresses
- A limited number of emails per day

To move out of the sandbox:

1. Go to the SES console
2. Click on "Account dashboard"
3. Under "Production access", click "Request production access"
4. Fill out the form with your use case details
5. Wait for AWS approval (usually takes 1-2 business days)

## Development

### Running the development environment

```bash
composer dev
```

This will start the Laravel server, queue worker, and Vite development server.

### Running tests

```bash
composer test
```

## License

This project is open-sourced software licensed under the MIT license.

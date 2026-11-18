# EduEnroll

 EduEnroll is a course and student enrollment portal for Northeastern Cebu
Colleges.

## What it does

- Anyone can browse, search, and filter courses.
- Students can register, verify their email, request one course, and track
  enrollment or cancellation requests.
- Administrators can review student applications and course requests, manage
  academic years, and approve or reject requests.
- The Relationships page shows how students, courses, and organizations connect.

## How to use

### Students

1. Choose **Register** and enter your details. Use a unique email address.
2. Open the verification email and click the link. Links expire after 60 minutes.
3. Sign in, browse the catalog, and select a course to request enrollment.
4. Check **Student Portal** to see your courses and request status.
5. To change courses, cancel your pending request or ask to cancel your current
   enrollment. Wait for admin approval before requesting another course.
6. Choose **Sign Out** when finished.

There are two student types: Incoming 1st Year and Transferee. The catalog has
nine courses in five organizations: EDSO, ITO, TMS, HMS, and JMS.

### Administrators

1. Sign in through the same `/login` page as students.
2. Use **Enrollment Requests** to view admission applications and course
   requests.
3. Approve or reject applications. To approve an application, choose an
   academics year and organization.
4. Approve or reject course requests and cancellation requests.
5. Add academic years and select the active year when needed. Review accepted
   student history by year and organization.

Approval emails include the student's document checklist and course status.
The admin page manages requests, not course records; the course catalog comes
from the database seeder.

## Security and form behavior

- Five failed sign-in attempts for the same email and IP address lock login for
  five minutes.
- Protected student and admin pages require sign-in. Student Portal access
  also requires a verified email.
- Each email can be used to register once.
- Login and registration forms prevent repeat button submissions and clear
  entered data when refreshed.

## Run locally

Requirements: PHP 8.2+, Composer, Node.js/npm, and SQLite (the default database).

Run these commands from the folder containing `artisan`:

```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Open the address shown by `php artisan serve` (usually
`http://127.0.0.1:8000`). Seeding is for a fresh database; don't run it again
against an already populated database.

By default, verification and status emails are written to
`storage/logs/laravel.log`, so registration works without an external mail
account. Configure SMTP below when you want emails delivered to inboxes.

Create an administrator from the terminal:

```powershell
php artisan admin:create
```

Follow the prompts. The password is hidden while you type. For frontend
development, run `npm run dev` in another terminal.

## Email setup (Gmail)

To send verification and status emails, use a Gmail App Password (not your
regular Gmail password). Add these settings to `.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-address@gmail.com
MAIL_PASSWORD=your-16-character-google-app-password
MAIL_FROM_ADDRESS="${MAIL_USERNAME}"
MAIL_FROM_NAME="${APP_NAME}"
APP_URL=http://127.0.0.1:8000
```

Enable 2-Step Verification and create the App Password in your Google account.
Then run `php artisan config:clear`. Set `APP_URL` to your actual site address.
Keep `.env` and passwords private. If mail fails, check your Gmail settings and
`storage/logs/laravel.log`.

## Main pages

- `/` — home
- `/courses` — course catalog and search
- `/courses/{id}` — course details and enrollment
- `/relationships` — model relationship demo
- `/register` and `/login` — account registration and sign-in
- `/email/verify` — email verification page
- `/student/dashboard` — verified student's portal
- `/admin/enrollment-requests` — administrator dashboard

## Model relationships

- A **User** has one **StudentProfile** and one **EnrollmentRequest**.
- A **User** can have many **CourseEnrollmentRequest** records.
- **Users** and **Courses** have a many-to-many relationship through
  `course_user`. This table stores the actual student-course enrollment.
- A **Department** has many **Courses**. It can also access student profiles
  through those courses.
- A **Course** belongs to a **Department** and has many student profiles and
  course requests.
- A **StudentProfile** belongs to a User and a Course.
- An **EnrollmentRequest** belongs to a User, an AcademicPeriod, and a
  Department.
- A **CourseEnrollmentRequest** belongs to a User and a Course.

`EnrollmentRequest` is for admission. `CourseEnrollmentRequest` is for choosing
or cancelling a course. When a course request is approved, the student is
linked to the course through `course_user`.

## Tests

```powershell
php artisan test
```

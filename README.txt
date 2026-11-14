DIGITAL STUDENT ATTACHMENT TRACKING SYSTEM
=========================================

CONTENTS
--------
index.php          Homepage
dashboard.php      Summary dashboard
register.php       Add student attachment records
search.php         Search by admission number or student name
tracking.php       Update status, progress and supervisor remarks
reports.php        Summary and printable attachment register
db_connect.php     MySQL database connection
header.php         Shared page header and navigation
footer.php         Shared page footer
style.css          Website styling
script.js          Small interactive features
setup.sql          Database and table creation script

INSTALLATION USING XAMPP
------------------------
1. Install and open XAMPP.
2. Start Apache and MySQL. Both should show as running.
3. Copy the whole StudentAttachmentSystem folder into:
   C:\xampp\htdocs\
4. Open your browser and visit:
   http://localhost/phpmyadmin
5. Click the Import tab.
6. Choose the setup.sql file from this folder.
7. Click Import (or Go). This creates the student_attachment_db database
   and the students table.
8. Open the website:
   http://localhost/StudentAttachmentSystem/

IMPORTANT
---------
- Keep all these files together in the StudentAttachmentSystem folder.
- Do not open PHP files by double-clicking them. Use the localhost address.
- The default XAMPP database settings in db_connect.php are:
  host: localhost
  username: root
  password: (blank)
  database: student_attachment_db
- If your MySQL root account has a password, update db_connect.php.
- This is a local demonstration/student project. Before real institutional use,
  add user authentication, role permissions, backups, HTTPS and stronger
  security controls. Do not put real student personal data into a public demo.

FIRST TEST
----------
1. Open the homepage.
2. Select Register.
3. Add a test student using made-up sample details.
4. Open Search to find that student.
5. Open Tracking to update the student's status and progress.
6. Open Dashboard and Reports to see the updated figures.

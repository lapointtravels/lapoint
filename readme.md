![Lapoint Logotype](https://pbs.twimg.com/profile_images/763316049840406528/mXVje-6v.jpg)
# Lapoint Web
### Prerequisites
Before getting started with setting up the Lapoint project please make sure you have following installed:
- MySQL
- PHP version 7.0 or later

### Installation guide
##### 1. Install wp-cli
In order to handle WordPress installs easily, install the `wp-cli`. Follow the instructions on http://wp-cli.org/.
**Note:** You don't need to have WordPress installed before.

##### 2. Clone the Lapoint repository
Run `git clone git@github.com:lapointtravels/Lapoint.git` to clone the Lapoint Web project.

##### 3. Install the WordPress core
Enter the project directory and run `wp core download`. This will install the WordPress core for the project (the WordPress core is git ignored).
##### 4. Setup DB
###### GET A DB DUMP OF STAGING
To get a dump of the staging DB, go to [phpMyAdmin](https://my.wpengine.com/installs/lapoint/phpmyadmin) and select the Export tab in the top menu. Select **Custom** as export method and **snapshot_lapoint**  in the databases section. Apart from that you can use the default settings. Click **GO**.



###### CREATE THE LOCAL PROJECT DB
Login to your MySQL in the console, i.e. `mysql -u [username]`.
Create a new DB `CREATE DATABASE lapoint_web;`.
Exit the MySQL console.
Before importing the DB, open the file in a text editor. Replace `snapshot_lapoint` with `lapoint_web` in lines 21-28.
Import the DB dump by running:
`mysql -u [username] -p lapoint_web < [database dump].sql`.

To verify that the import was successful run `mysql -u [username] -p lapoint_web` and then `SHOW TABLES;`. This should display a long list of table names.

###### UPDATE PROJECT CONFIGURATION
Open the `wp-config.php` in the project root. Update the DB config to match your setup -  `DB_NAME`, `DB_USER`, `DB_PASSWORD`.

##### 5. Get the project images
Since the `/uploads` directory is huge in this project it is currently git ignored. This means you have to get it yourself manually if you want things to look neat, preferably accessing WP Engine with SFTP. The `/uploads` directory is available in `/wp-content`. Get it and move it to the corresponding place in the project.

##### 6. Run the project
To run the project successfully in a local environment we need to use ngrok.
[Download ngrok](https://ngrok.com/), unzip it and move it to your `/usr/local/bin` directory.
[Sign in](https://dashboard.ngrok.com/user/login) using your GitHub account and add the auth token as instructed.

Run `wp server` in the console to run the project. In another tab, run `ngrok http 8080`. Once ngrok is initiated you will receive the url to use.

... and you're done! :)

### Workflows and deployment
For everyone's convinience follow the workflow below when working with the Lapoint web project.
1. Check out a new branch everytime you initiating some kind of work. **Avoid working directly in master branch!**
2. When your work is ready push the branch and create a pull request.
3. If working in pair/group please review each other's changes.
4. Do a squash merge into master and **add a proper message of what is done**.
5. Pull master branch locally and then run `git push staging master` to push it to the staging environment on WP engine.
6. Let Lapoint verify the changes on http://lapoint.staging.wpengine.com.
7. If changes are accepted, [promote staging to production](https://lapoint.wpengine.com/wp-admin/admin.php?page=wpengine-staging). Otherwise make additional changes using the workflow above.

### Notes
###### DEPLOYMENT
In order to be able to push to the WP engine environment you need to [add your public SSH key](https://my.wpengine.com/installs/lapoint/git_push).

###### SYNCING DB AND UPLOADS
 If you for some reason get a new dump of the staging DB you might need to also fetch the latest version of the `/uploads` directory to keep everything in sync.

###### ENVIRONMENTS
**Staging:** http://lapoint.staging.wpengine.com

**Production:** http://lapoint.wpengine.com

To access the admin console locally use the same credentials as in the production environment.

###### WPML
The WMPL plugins needs to be installed locally. Currently following plugins are used:
- wpml-sticky-links
- wmpl-string-translation
- wpml-translation-management
- wpml-widgets

















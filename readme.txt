=== BuddyPress Groupblog ===
Contributors: Rodney Blevins & Marius Ooms
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7374704
Tags: buddypress,groups,blogs,content
Requires at least: WPMU 2.8 / BP 1.1
Tested up to: WPMU 2.8.4a
Stable tag: 1.2.2

BuddyPress Groupblog extends the group functionality by enabling the group to have a single blog associated with it. Group members are automatically added to the blog and will have roles as set by the group admin.

== Description ==

The BuddyPress Groupblog plugin extends the group functionality by enabling each group to have a single blog associated with it. Group members are automatically added to the blog and will have blog roles as set by the groupblog admin settings.

**Features:**

* Full blog theme integration. The included bp-groupblog theme mimics the group pages.
* WP Admin option to set default blog for groups plus bonus options.
* Automated blog registration at group creation stage.
* Blog privacy settings are initially inherited from group privacy settings.
* Group members are automatically added to the blog.
* Blog roles match group roles as set by the group admin.
* Solid error checking that the blog follows validation.
* Group admin tab to access the group-blog settings.
* Recent posts are displayed on the group home page, much like the forum topics.
* A menu tab is added to display the latest blog activity and blog page links.
* Blog themes will have the ability to pull in group info and create a theme that could resemble the group exactly.
* Leaving the group will downgrade the member role to 'subscriber'.
* Allow the group admin to select one of his/her existing blogs.

**Roadmap:**

* Include an RSS icon for easy access to the Blog's RSS feed.

== Installation ==

1) unzip the bp-groupblog.zip file into `/wp-contents/plugins/bp-groupblog`

2) move the `/bp-groupblog/themes/bp-groupblog` folder to your WPMU themes folder

3) activate the plugin

4) You are done!

5) If you want to override the groupblog theme files, just copy the 'groupblog' folder into your active theme and modify to your needs.

**NOTE: Please deactivate the plugin before running automatic upgrade or you will get a big fat 'Cannot redeclare' fatal error. Regardless, if you do activate while the plugin is active it will still work fine. It is just that nobody likes errors, even when they are not real.**

== Screenshots ==

1. Screenshot of the group blog creation stage.
2. Screenshot of the group blog page.

== Changelog == 

= 1.2.2 =
* Added a function to check BuddyPress is loaded first

= 1.2.1 =
Updated language files

= 1.2 =
* Added admin settings screen. You can now set the default groupblog theme, plus some bonus options.
* Changed some code here and there.
* Including a groupblog theme based on the bp-sn-parent theme. You can use this theme to make wpmu blogs resemble the BuddyPress group and display group related content inside the groupblog theme.

= 1.1.6 =
* Added a message to inform the user that a groupblog is only chosen once.

= 1.1.5 =
* Fixed a bug where the member roles were not being updated upon group creation.

= 1.1.4 =
* Added file_exists to various files, so templates can be overridden in the theme. This also means we had to move the resource folders back into the theme folder.

= 1.1.3 =
* Now using plugin-template.php, so there is no longer a need to move the groupblog folder to the active theme folder. Also updating the plugin through the wordpress plugin interface now works without problems.

= 1.1.2 = 
* Updated language files.

= 1.1.1 = 
* Improved jquery handeling on check boxes and radio buttons.

= 1.1 =
* If a member leaves the group they will now be downgraded to 'subscriber'.
* Added template tags: groupblog_blog_id($group_id), get_groupblog_blog_id($group_id), groupblog_group_id($blog_id), get_groupblog_group_id($blog_id)
* Group admin now has the option of either creating a new blog or associating one of his blogs with the new group.
* Moved css, images and js folders to the theme folder.
* Added Jquery to give feedback to user input.
* Fixed a bug which would cause the group creator to be demoted to subscriber of his own blog if member blogging.

= 1.0 =
* Initial release.

= The changelog can also be found here: =
http://plugins.trac.wordpress.org/log/bp-groupblog?verbose=on
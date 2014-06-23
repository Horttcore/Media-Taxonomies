=== Media Taxonomies ===
Contributors: Horttcore
Donate link: http://horttcore.de
Tags: media, taxonomies
Requires at least: 3.8
Tested up to: 4.0
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress taxonomies for media files.

== Description ==

WordPress taxonomies for media files.
Plugin ships with media-category and media-tag taxonomy

== Installation

* Put the plugin file in your plugin directory and activate it in your WP backend.
* Go to your media library and add some categories

== FAQ

= Why does my term count not match if I add another taxonomy?

This might be a failure in your register_taxanomy function.
You have to set `update_count_callback` to `_update_generic_term_count` in the taxonomy registration
Visit for more information http://codex.wordpress.org/Function_Reference/register_taxonomy

= I added another taxonomy for attachments, but don't want to use the features of this plugin.

Ok, use the `media-taxonomies` hook to remove your taxonomy from the list

= Known Bugs

* Registering a core taxonomy via `register_taxonomy_for_object_type( 'post_tag', 'attachment' )` won't work

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog

= 1.3

* Add new terms functionality
* Small improvements

= 1.2.2

* Small improvements

= 1.2.1

* Enhancement: Modal select box label changed to use WordPress standard - props Dirk

= 1.2

* Bugfix: Media modal filter did not work, broke due last versions fix

= 1.1

* Bugfix: Media overview filter did not work

= 1.0

* Added integrated modal filter - props Wyck
* Added german language file

= 0.9.1

* Added modal filter

= 0.9

* Preview version

== ToDo

* implement wp.media like 'All' selection, this is currently achieved by a workaround

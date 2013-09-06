Media-Taxonomies
================

WordPress taxonomies for media files

## Installation

* Put the plugin file in your plugin directory and activate it in your WP backend.
* Go to your media library and add some categories

## FAQ

### Can I add more taxonomies for attachments?

Yes, but the plugin does not support all features yet.
I'll try to make it possible in release v1.0

### Why does my term count not match if I add another taxonomy?

This might be a failure in your register_taxanomy function.
You have to set `update_count_callback` to `_update_generic_term_count` in the taxonomy registration
Visit for more information http://codex.wordpress.org/Function_Reference/register_taxonomy

### I added another taxonomy for attachments, but don't want to use the features of this plugin.

Ok, use the `media-taxonomies` hook to remove your taxonomy from the list

## Changelog

### 0.9.1

* Added modal filter

### 0.9

* Preview version

## ToDo

* implement wp.media like 'All' selection, this is currently achieved by a workaround
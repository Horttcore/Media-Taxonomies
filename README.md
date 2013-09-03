Media-Taxonomies
================

WordPress taxonomies for media files

## Installation

* Put the plugin file in your plugin directory and activate it in your WP backend.
* Go to your media library and add some categories

## FAQ

### Why does my term count not match if I add another taxonomy?

This might be a failure in your register_taxanomy function.
You have to set `update_count_callback` to `_update_generic_term_count` in the taxonomy registration
Visit for more information http://codex.wordpress.org/Function_Reference/register_taxonomy

## Changelog

### 0.9

* Preview version
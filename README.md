Middlebury Event Sync
=====================

This module provides an interface between calendaring systems (Currently R25)
and Drupal, importing calendar events as Drupal nodes. Calendar events will be
associated with a location taxonomy, the terms of which hold location details.

Importing locations
-------------------

Because the R25 event feeds include only the formal name of the location and not
any other details (such as the room-code, building-code, or address), this data
needs to be sourced from other locations.

Staff in the scheduling office can export an Excel spreadsheet of all location
details. These can then be converted via the `locations_to_yaml` Drupal CLI
command to convert them into a YAML file that can then be imported into the site
using the `yaml_content` module.

At this point, address details must be added manually. This may be scripted in
the future if a data-source can be located.

Steps to import/update the location taxonomy:

# Get the location Excel XML export from Kristina Simmons or someone else in the
  scheduling office. There should be one called something like
  `All Monterey Spaces 2017.xml` and one called
  `All Middlebury Spaces 2017.xml`.

# Put that xml file on the dev server somewhere, such as your home directory.

# `cd` to the drupal site-root for the site you want to update. e.g.

      cd ~/drupal8-institute/

# Run the `locations_to_yaml` command to update the YAML file with updated data:

      drupal middlebury_event_sync:locations_to_yaml profiles/institute_profile/
      content/locations.content.yml ~/All\ Monterey\ Spaces\ 2017.xml

# Use `yaml_content` to import/update the taxonomy in dev and verify no errors:

      drush8 yaml-content-import-profile institute_profile

# Commit changes to the institute_profile and deploy to production.

# In production, import/update the institute_profile using `yaml_content`:

      drush8 yaml-content-import-profile institute_profile

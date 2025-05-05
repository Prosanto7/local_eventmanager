# local_eventmanager
Allows site admins and managers to create and manage institution-wide events (e.g. workshops, webinars, exam dates)

---
Let’s walk through the **step-by-step development** of a **Moodle local plugin** called `local_eventmanager` that supports CRUD operations and role-based views (Admin/Manager vs. Student).

## 🔧 **Plugin Overview**
- **Plugin Type:** Local plugin (`local_eventmanager`)
- **Function:** CRUD interface for institution-wide events
- **Permissions:**
  - **Admin/Manager:** Full CRUD (Create, Read, Update, Delete)
  - **Student:** View only (list/detail)

---

## 📁 **1. Plugin Folder Structure**
Create the directory:

```
moodle/local/eventmanager/
```

Inside it, create these files:

```
.
├── db
│   ├── access.php
│   └── install.xml
├── index.php
├── manage.php
├── edit.php
├── delete.php
├── view.php
├── version.php
├── lang
│   └── en
│       └── local_eventmanager.php
```


## 📜 **2. version.php**
```php
<?php
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_eventmanager';
$plugin->version = 2025043006;
$plugin->requires = 2022041900;
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.0.0';
```

## 🛠️ **3. db/install.xml** (Defines the event table)

Create with Moodle's XMLDB editor or use this:
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<XMLDB PATH="local/eventmanager/db" VERSION="2025043000" COMMENT="Event manager table">
  <TABLES>
    <TABLE NAME="local_eventmanager" COMMENT="Stores event data">
      <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="title" TYPE="char" LENGTH="255" NOTNULL="true"/>
        <FIELD NAME="description" TYPE="text" NOTNULL="false"/>
        <FIELD NAME="format" TYPE="int" LENGTH="2" NOTNULL="false"/>
        <FIELD NAME="category" TYPE="char" LENGTH="100" NOTNULL="true"/>
        <FIELD NAME="eventdate" TYPE="int" LENGTH="10" NOTNULL="true"/>
        <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true"/>
      </FIELDS>
      <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
      </KEYS>
    </TABLE>
  </TABLES>
</XMLDB>
```

## 🔐 **4. db/access.php** (Role-based capabilities)

```php
<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/eventmanager:manageevents' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ]
    ]
];
```

## 🌐 **5. lang/en/local_eventmanager.php**

```php
<?php
$string['pluginname'] = 'Event Manager';
$string['newevent'] = 'New Event';
$string['editevent'] = 'Edit Event';
$string['deleteevent'] = 'Delete Event';
$string['eventtitle'] = 'Title';
$string['eventdesc'] = 'Description';
$string['eventdate'] = 'Event Date';
$string['eventcategory'] = 'Category';
$string['viewdetails'] = 'View Details';
$string['manageevents'] = 'Manage Events';
$string['viewevents'] = 'View Events';
$string['cancel_form'] = 'You cancelled the form action';
$string['updatethanks'] = 'Event updated successfully';
$string['institutionevents'] = 'Institution Events';
$string['eventmanager:viewevents'] = 'View Events';
$string['eventmanager:manageevents'] = 'Manage Events';
```

## 🧾 **6. templates/eventlist.mustache**

```php
<?php
require('../../config.php');
require_login();

$id = required_param('id', PARAM_INT);
$event = $DB->get_record('local_eventmanager', ['id' => $id], '*', MUST_EXIST);

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/eventmanager/view.php', ['id' => $id]));
$PAGE->set_title($event->title);
$PAGE->set_heading($event->title);

echo $OUTPUT->header();
echo format_text($event->description);
echo html_writer::tag('p', "Category: " . $event->category);
echo html_writer::tag('p', "Date: " . userdate($event->eventdate));
echo $OUTPUT->footer();
```

## 🧑‍💻 **7. index.php** (Entry point)

```php
<?php
require('../../config.php');
require_login();

$context = context_system::instance();
$canmanage = has_capability('local/eventmanager:manageevents', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/eventmanager/index.php'));
$PAGE->set_title('Event Manager');
$PAGE->set_heading('Event Manager');

$events = $DB->get_records('local_eventmanager', null, 'eventdate ASC');

// Prepare data for Mustache
$templatecontext = [
    'heading' => get_string('institutionevents', 'local_eventmanager'),
    'canmanage' => $canmanage,
    'newevent' => get_string('newevent', 'local_eventmanager'),
    'newurl' => new moodle_url('/local/eventmanager/edit.php'),
    'events' => !empty($events),
    'list' => []
];

foreach ($events as $event) {
    $item = [
        'title' => format_string($event->title),
        'eventdate' => userdate($event->eventdate),
        'viewurl' => new moodle_url('/local/eventmanager/view.php', ['id' => $event->id])
    ];

    if ($canmanage) {
        $item['canmanage'] = true;
        $item['editurl'] = new moodle_url('/local/eventmanager/edit.php', ['id' => $event->id]);
        $item['deleteurl'] = new moodle_url('/local/eventmanager/delete.php', ['id' => $event->id]);
    }

    $templatecontext['list'][] = $item;
}


echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_eventmanager/eventlist', $templatecontext);
echo $OUTPUT->footer();

```

## 📝 **8. edit.php** (Create or update)

```php
<?php
require('../../config.php');
use local_eventmanager\form\event_form;

require_login();

$context = context_system::instance();
require_capability('local/eventmanager:manageevents', $context);

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT);
$event = $id ? $DB->get_record('local_eventmanager', ['id' => $id], '*', MUST_EXIST) : null;

$mform = new event_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/eventmanager/index.php'), get_string('cancel_form', 'local_eventmanager'));
} else if ($data = $mform->get_data()) {
    $record = (object)[
        'title' => $data->title,
        'description' => $data->description['text'],
        'format' => $data->description['format'],
        'category' => $data->category,
        'eventdate' => $data->eventdate,
        'timecreated' => time()
    ];
    if ($data->id) {
        $record->id = $data->id;
        $DB->update_record('local_eventmanager', $record);
    } else {
        $DB->insert_record('local_eventmanager', $record);
    }
    redirect(new moodle_url('/local/eventmanager/index.php'), get_string('updatethanks', 'local_eventmanager'));
}

if ($event) {
    $event->description = [
        'text' => $event->description,
        'format' => $event->format
    ];
    $mform->set_data($event);
}

$PAGE->set_url(new moodle_url('/local/eventmanager/edit.php', ['id' => $id]));
$PAGE->set_title('Edit Event');
$PAGE->set_heading('Edit Event');

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
```

## 🗑️ **9. delete.php**

```php
<?php
require('../../config.php');
require_login();
require_capability('local/eventmanager:manageevents', context_system::instance());

$id = required_param('id', PARAM_INT);
$event = $DB->get_record('local_eventmanager', ['id' => $id], '*', MUST_EXIST);

$DB->delete_records('local_eventmanager', ['id' => $id]);
redirect('index.php');
```

## 🔍 **10. view.php**

```php
<?php
require('../../config.php');
require_login();

$id = required_param('id', PARAM_INT);
$event = $DB->get_record('local_eventmanager', ['id' => $id], '*', MUST_EXIST);

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/eventmanager/view.php', ['id' => $id]));
$PAGE->set_title($event->title);
$PAGE->set_heading($event->title);

echo $OUTPUT->header();
echo format_text($event->description);
echo html_writer::tag('p', "Category: " . $event->category);
echo html_writer::tag('p', "Date: " . userdate($event->eventdate));
echo $OUTPUT->footer();
```

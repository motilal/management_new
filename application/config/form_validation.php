<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$config = array(
    'pages/manage' => array(
        array(
            'field' => 'title',
            'label' => 'Page title',
            'rules' => "trim|required|max_length[255]"
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'meta_keywords',
            'label' => 'Meta keywords',
            'rules' => "trim|max_length[1024]"
        ),
        array(
            'field' => 'meta_description',
            'label' => 'Meta description',
            'rules' => "trim|max_length[1024]"
        )
    ),
    'events/manage' => array(
        array(
            'field' => 'title',
            'label' => 'Event title',
            'rules' => "trim|required|max_length[255]"
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'start_date',
            'label' => 'Start Date',
            'rules' => "trim|required|callback__validate_dateformate"
        ),
        array(
            'field' => 'end_date',
            'label' => 'End Date',
            'rules' => "trim|required|callback__validate_dateformate|callback__validate_daterange"
        )
    ),
    'countries/manage' => array(
        array(
            'field' => 'name',
            'label' => 'Country Name',
            'rules' => "trim|required|max_length[200]"
        ),
        array(
            'field' => 'short_name',
            'label' => 'Short Name',
            'rules' => 'trim|max_length[50]'
        )
    ),
    'states/manage' => array(
        array(
            'field' => 'name',
            'label' => 'State Name',
            'rules' => "trim|required|max_length[200]|callback__validate_state_name"
        ),
        array(
            'field' => 'country_id',
            'label' => 'Country',
            'rules' => "trim|required"
        ),
        array(
            'field' => 'short_name',
            'label' => 'Short Name',
            'rules' => 'trim|max_length[200]'
        )
    ),
    'cities/manage' => array(
        array(
            'field' => 'name',
            'label' => 'City Name',
            'rules' => "trim|required|max_length[200]|callback__validate_city_name"
        ),
        array(
            'field' => 'country_id',
            'label' => 'Country',
            'rules' => "trim|required"
        ),
        array(
            'field' => 'state_id',
            'label' => 'State',
            'rules' => 'trim|required'
        )
    ),
    'email_templates/manage' => array(
        array(
            'field' => 'title',
            'label' => 'Title',
            'rules' => "trim|required|max_length[255]"
        ),
        array(
            'field' => 'subject',
            'label' => 'Subject',
            'rules' => "trim|required|max_length[255]"
        ),
        array(
            'field' => 'variable',
            'label' => 'Variable',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'body',
            'label' => 'Body',
            'rules' => 'trim|required'
        )
    ),
    'add_subadmins' => array(
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => "trim|required|max_length[50]"
        ),
        array(
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => "trim|max_length[50]"
        ),
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => "trim|required|valid_email|max_length[254]|callback__validate_email"
        ),
        array(
            'field' => 'phone',
            'label' => 'Phone',
            'rules' => "trim|max_length[20]"
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => "trim|required|max_length[255]"
        ),
        array(
            'field' => 'cpassword',
            'label' => 'Confrim Password',
            'rules' => "trim|matches[password]"
        )
    ),
    'edit_subadmins' => array(
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => "trim|required|max_length[50]"
        ),
        array(
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => "trim|max_length[50]"
        ),
        array(
            'field' => 'phone',
            'label' => 'Phone',
            'rules' => "trim|max_length[20]"
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => "trim|max_length[255]"
        ),
        array(
            'field' => 'cpassword',
            'label' => 'Confrim Password',
            'rules' => "trim|matches[password]"
        )
    ),
    'permissions/manage' => array(
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => "trim|required|max_length[255]"
        ),
        array(
            'field' => 'key',
            'label' => 'Key',
            'rules' => "trim|required|max_length[255]"
        ),
        array(
            'field' => 'group',
            'label' => 'group',
            'rules' => "trim|required|max_length[255]"
        ),
        array(
            'field' => 'order',
            'label' => 'Order',
            'rules' => "trim|max_length[11]"
        )
    )
);
$config['error_prefix'] = '<div class="help-block">';
$config['error_suffix'] = '</div>';

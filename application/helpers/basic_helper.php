<?php

if (!function_exists('pr')) {

    function pr($data = null, $exit = false) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        if ($exit === TRUE)
            die();
    }

}

if (!function_exists('create_pagination')) {

    /**
     *
     * @param type $url
     * @param type $total_rows
     * @param type $per_page
     * @param type $segment
     * @param type $query_string
     * @return type pagination
     */
    function create_pagination($url, $total_rows, $per_page, $segment, $query_gegments = array(), $config = array()) {
        $CI = & get_instance();
        if (empty($query_gegments)) {
            $query_gegments = $CI->input->get();
            if (isset($query_gegments['start'])) {
                unset($query_gegments['start']);
            }
        }
        $query_string = http_build_query($query_gegments);

        $config['base_url'] = site_url($url);
        $config['num_links'] = 2;
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['uri_segment'] = $segment;
        $config['suffix'] = $query_string ? "&" . $query_string : "";
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = "start";
        $config['first_url'] = $query_string ? "?" . $query_string : "";
        /* design */
        $config['prev_link'] = 'Prev';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['next_link'] = 'Next';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="javascript:;">';
        $config['cur_tag_close'] = '</a></li>';
        $CI->load->library("pagination");
        $CI->pagination->initialize($config);
        return $CI->pagination->create_links();
    }

}

if (!function_exists('create_unique_slug')) {

    function create_unique_slug($string, $table = 'pages', $field = 'slug', $key = NULL, $value = NULL) {
        $CI = & get_instance();
        $slug = url_title($string);
        $slug = strtolower($slug);
        $i = 0;
        $params = array();
        $params[$field] = $slug;
        if ($key)
            $params["$key !="] = $value;

        while ($CI->db->where($params)->get($table)->num_rows()) {
            if (!preg_match('/-{1}[0-9]+$/', $slug))
                $slug .= '-' . ++$i;
            else
                $slug = preg_replace('/[0-9]+$/', ++$i, $slug);

            $params [$field] = $slug;
        }
        return $slug;
    }

}

if (!function_exists('getLangText')) {

    /**
     * 
     * @param type $string
     * @return type
     */
    function getLangText($langKey) {
        $CI = & get_instance();
        return $CI->lang->line("$langKey", false);
    }

}
if (!function_exists('validateDate')) {

    function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}
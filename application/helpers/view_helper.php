<?php

if (!function_exists("sanitize_output")) {

    /**
     *
     * @param type $buffer
     * @return type 
     */
    function sanitize_output($buffer) {

        $search = array(
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );

        $replace = array(
            '>',
            '<',
            '\\1'
        );

        //$buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

}

if (!function_exists('sort_attribute')) {

    function sort_attribute($fieldname = null) {
        $CI = & get_instance();
        if (!empty($fieldname)) {
            $sort = 'asc';
            $sort_class = 'sorting';
            if ($CI->input->get('sort') == 'asc' && $CI->input->get('order_by') == $fieldname) {
                $sort = 'desc';
                $sort_class = 'sorting_asc';
            } else if ($CI->input->get('sort') == 'desc' && $CI->input->get('order_by') == $fieldname) {
                $sort = 'asc';
                $sort_class = 'sorting_desc';
            }
            return (object) array('sort' => $sort, 'sort_class' => $sort_class);
        }
    }

}

if (!function_exists('sorting_url')) {

    function sorting_url($fieldname = null) {
        $CI = & get_instance();
        if (!empty($fieldname)) {
            $sort_attr = sort_attribute($fieldname);
            $getdata = $CI->input->get();
            unset($getdata['sort']);
            unset($getdata['order_by']);
            $query_string = http_build_query($getdata);
            $ext_url = "";
            if (!empty($query_string)) {
                $ext_url = '&' . $query_string;
            }
            $url = current_url() . "?sort={$sort_attr->sort}&order_by={$fieldname}{$ext_url}";

            return (object) array('url' => $url, 'class' => $sort_attr->sort_class);
        }
    }

}
if (!function_exists('dataTableGetRequest')) {

    function dataTableGetRequest($getData, $colomn) {
        $order = array();
        $limit = array();
        $search = '';
        if (!empty($getData['order'][0]['column']) && !empty($getData['order'][0]['dir'])) {
            $colomnIndex = $getData['order'][0]['column'];
            if (isset($colomn[$colomnIndex])) {
                $order = array($colomn[$colomnIndex], $getData['order'][0]['dir']);
            }
        }
        if (isset($getData['start']) && !empty($getData['length'])) {
            $limit = array('start' => $getData['start'], 'limit' => $getData['length']);
        }
        if (!empty($getData['search']['value'])) {
            $search = $getData['search']['value'];
        }
        return (object) array('order' => $order, 'limit' => $limit, 'search' => $search);
    }

}

if (!function_exists('getPageSerial')) {

    function getPageSerial($total, $start, $key) {
        $page = 0;
        if ($start > 0) {
            $page = $start / $total;
        }
        return $page * $start + ($key + 1);
    }

}
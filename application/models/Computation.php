<?php

require(__DIR__ . '/Day.php');

class Computation extends Base_Model {

    function __construct() {
        parent::__construct();
    }

    /**
     * Entry get function searches the params for the type of query being done and returns associated function.
     * This is based on the "query" param.
     * So far the only query supported is "average" which requires the user_id param
     * @param $data
     * @return stdClass
     * @throws ErrorException
     */
    public function get($data) {
        $query = isset($data['query']) ? $data['query'] : null;
        if (!$query) {
            throw new ErrorException("You must provide a 'query' to use the computation endpoint.");
        }

        if ($query === 'average_sleep_time') {
            return $this->get_average_sleep_time($data);
        }
        // else if ($query === 'etc...') { ... etc ... }

        throw new ErrorException("Your query '$query' is not supported.");
    }

    /**
     * Gets the average sleep time for a given user based on their ID.
     * Query for all the day recoreds for this user.
     *
     * @param $data
     * @return stdClass
     * @throws ErrorException
     */
    public function get_average_sleep_time($data) {
        $id = isset($data['user_id']) ? $data['user_id'] : null;
        if (!$id) {
            throw new ErrorException("You must supply a 'user_id' to get the average sleep time.");
        }

        // Set up the output object.
        $out = new stdClass();
        $out->user_id = $data['user_id'];

        // Query for all the days for this user :
        $day = new Day();
        $res = $day->get_by_uid($id);

        if (!count($res)) {
            throw new ErrorException("Your query for average sleep times returned no results for user $id");
        }

        // Now calculate the sum for this user. Each time slot that is marked as 'ASLEEP' is 15 minutes, divide by
        // 60 for hours then by the number of days recorded for the average.
        $total_arr = [];
        foreach ($res as $key=>$day) {
            $total = 0;
            foreach ($day as $field=>$val) {
                if ($val === 'ASLEEP') {
                    $total += 15;
                }
            }
            $total_arr[]= $total / 60;
        }
        $out->average = array_sum($total_arr) / count($total_arr);
        return $out;
    }
}

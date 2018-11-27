<?php
/* A MinMax heap as described in
 *   <http://www.cs.otago.ac.nz/staffpriv/mike/Papers/MinMaxHeaps/MinMaxHeaps.pdf>
 *
 * Public API:
 *  - count() - returns the number of elements on the heap
 *  - peek_min() - returns the minimum value
 *  - peek_max() - returns the maximum value
 *  - get_min() - removes the minimum value and returns it
 *  - get_max() - removes the maximum value and returns it
 *  - insert($val) - adds $val to the heap
 *
 *  - compare($a, $b) - takes two list indexes, returns 0 if the values are
 *      equal, returns 1 if $a > $b, returns -1 if $a < $b. You may override
 *      this method in order to handle more complex objects.
 */
class MinMaxHeap {
    protected $list;
    function __construct() {
        $this->list = array();
    }
    public function count() {
        return count($this->list);
    }
    public function peek_min() {
        if ($this->count() == 0) return null;
        $rv = $this->list[0];
        return $rv;
    }
    public function get_min() {
        if ($this->count() == 0) return null;
        $rv = $this->list[0];
        # Remove the last element
        $last = array_pop($this->list);
        if ($this->count() > 0) {
            # Place it in the root
            $this->list[0] = $last;
            # Trickle down
            $this->trickle_down(0);
        }
        # Return the old root
        return $rv;
    }
    public function peek_max() {
        if ($this->count() == 0) return null;
        $max = $this->get_max_id();
        return $this->list[$max];
    }
    public function get_max() {
        if ($this->count() == 0) return null;
        $max = $this->get_max_id();
        # Remove the last element
        $last = array_pop($this->list);
        if ($this->count() > $max) {
            # Place it in the max
            $this->list[$max] = $last;
            # Trickle down
            $this->trickle_down($max);
        }
        # Return the old root
        return $rv;
    }
    private function get_max_id() {
        if ($this->count() == 0) return null;
        $max = 0;
        if ($this->count() >= 2)
            $max = 1; # left root child is definitely bigger
        if ($this->count() >= 3 and $this->compare(2, 1) == 1)
            $max = 2; # maybe right root child is bigger still
        return $max;
    }
    public function insert($val) {
        # Place this value at the end
        $i = array_push($this->list, $val) - 1;
        # Bubble up
        $this->bubble_up($i);
    }
    private function trickle_down($i) {
        $dir = $this->get_level_direction($i);
        $this->trickle_down_r($i, $dir);
    }
    private function trickle_down_r($i, $dir) {
        # If list[i] has children then
        if ($this->count() > $i * 2 + 1) {
            # Find the index of the smallest of children and grandchildren
            $m = $i*2 + 1;
            foreach( array($i*2+2, $i*4+3, $i*4+4, $i*4+5, $i*4+6) as $j ) {
                if (isset($this->list[$j]) and
                    $this->compare($j, $m) == $dir)
                    $m = $j;
            }
            # If m < i
            if ($this->compare($m, $i) == $dir) {
                # If m is a granchild then
                if ($m >= $i*4 + 3) {
                    # Swap list[m] and list[i]
                    $tmp = $this->list[$m];
                    $this->list[$m] = $this->list[$i];
                    $this->list[$i] = $tmp;
                    # If list[m] is now > list[its parent]
                    $mparent = floor(($m-1) / 2);
                    if ($this->compare($m, $mparent) == -$dir) {
                        # Swap list[m] and list[its parent]
                        $tmp = $this->list[$m];
                        $this->list[$m] = $this->list[$mparent];
                        $this->list[$mparent] = $tmp;
                    }
                    # trickle_down_r(m)
                    $this->trickle_down_r($m, $dir);
                # else, m is a child
                } else {
                    # Swap list[m] and list[i]
                    $tmp = $this->list[$m];
                    $this->list[$m] = $this->list[$i];
                    $this->list[$i] = $tmp;
                }
            }
        }
    }
    private function bubble_up($i) {
        $dir = $this->get_level_direction($i);
        # If list[i] has a parent and list[i] > list[parent]
        $iparent = floor(($i-1) / 2);
        if ($i > 0 and $this->compare($i, $iparent) == -$dir) {
            # swap list[i] and list[parent]
            $tmp = $this->list[$i];
            $this->list[$i] = $this->list[$iparent];
            $this->list[$iparent] = $tmp;
            # bubble_up_r(parent)
            $this->bubble_up_r($iparent, -$dir);
        # else
        } else {
            # bubble_up_r(i)
            $this->bubble_up_r($i, $dir);
        }
    }
    private function bubble_up_r($i, $dir) {
        # If list[i] has grandparent
        if ($i > 2) {
            # If list[i] < list[grandparent]
            $igp = floor((floor(($i-1) / 2)-1) / 2);
            if ($this->compare($i, $igp) == $dir) {
                # swap list[i] and list[grandparent]
                $tmp = $this->list[$i];
                $this->list[$i] = $this->list[$igp];
                $this->list[$igp] = $tmp;
                # bubble_up_r(grandparent)
                $this->bubble_up_r($igp, $dir);
            }
        }
    }
    private function get_level_direction($i) {
        return (floor(log($i+1, 2)) % 2 == 1) ? 1 : -1;
    }
    protected function compare($i, $j) {
        if ($i == $j or $this->list[$i] == $this->list[$j]) return 0;
        return ($this->list[$i] < $this->list[$j]) ? -1 : 1;
    }
}
?>

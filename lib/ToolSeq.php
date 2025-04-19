<?php

class ToolSeq
{

    /**
     * Get the next number of a numeric sequence
     * @param string $seq
     * @param string $start use this to force a start prefix (ex '0001')
     */
    public static function nextSeq($seq, $start = '')
    {
        $d = self::decodeLabel($seq);
        if ($start != '' && $d['nextSuffix'] == '') {
            return $d['prefix'] . $start;
        }
        else {
            return $d['prefix'] . $d['nextSuffix'];
        }
    }
    // --------------------------------------------------------------------

    /**
     * Input: label formatted as AAABBBB where BBBB is a left-zero-filled number
     * Output: array
     * prefix => AAA
     * suffix => BBBB
     * value => (int) BBBB
     * nextSuffix => BBBB + 1
     * nextValue => (int) BBBB+1
     * length => number of digits of BBBB
     *
     * @param string $label
     * @return type
     */
    public static function decodeLabel($label)
    {
        $ret = array('length' => 0, 'value' => 0, 'suffix' => '', 'nextSuffix' => '', 'nextValue' => 0);
        // Let's see if is there a number at the end of the string
        if (preg_match('/([0-9]+)$/u', $label, $m, PREG_OFFSET_CAPTURE) == 1) {
            // Capture the prefix of the sequence (the string before the number)
            $ret['prefix'] = mb_substr($label, 0, $m[1][1], 'UTF-8');
            // This is the length of the number (useful if need to fill with zeroes)
            $length        = mb_strlen($m[1][0], 'UTF-8');
            $value         = intval($m[1][0], 10);
            $ret['length'] = $length;
            $ret['value']  = $value;
            $ret['suffix'] = sprintf("%0{$length}u", $value);
            // The incremented number
            $ret['nextSuffix'] = sprintf("%0{$length}u", $value + 1);
            $ret['nextValue']  = $value + 1;
        }
        else {
            $ret['prefix'] = $label;
        }
        return $ret;
    }
    // --------------------------------------------------------------------
}

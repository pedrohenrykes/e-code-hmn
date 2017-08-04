<?php

class UsefulFunctions
{
    public static function ageCalculation( $value = NULL )
    {
        if( ( !empty( $value ) ) && ( strlen( $value ) == 10 ) )
        {
            $dateBegin = explode( '/', $value );
            $dateEnd   = explode( '/', date( 'd/m/Y' ) );

            $timeBegin = mktime( 0, 0, 0, $dateBegin[ 1 ], $dateBegin[ 0 ], $dateBegin[ 2 ] );
            $timeEnd   = mktime( 0, 0, 0, $dateEnd[ 1 ],   $dateEnd[ 0 ],   $dateEnd[ 2 ] );

            $seconds = $timeEnd - $timeBegin;

            $days = ( int ) floor( $seconds / ( 60 * 60 * 24 ) );

            return ( int ) ( $days / 365.25 );
        }
        else
        {
            return 0;
        }
    }
}

<?php
class StreemeUtil
{
  /**
  * Format and encode a filesystem name into an iTunes style format
  * @param filename str: the input filename to encode
  * @return       str: the iTunes formatted semi-urlencoded file or false
  */
  public static function itunes_format_encode( $filename )
  {
    if ( !isset( $filename ) || empty( $filename ) ) return false;
    
    //explode filename into parts by directory separator
    $file_parts = explode( '/', $filename );
    
    $accumulator = array();
    if( count( $file_parts ) > 0 )
    {
      //urlencode each part
      foreach( $file_parts as $part )
      {
      	//encode windows drive letters a bit differently, like itunes does?
      	if ( strpos( $part, ':' ) )
      	{
      	  $accumulator[] = $part;
      	  continue;
      	}
        $accumulator[] = rawurlencode( $part );
      }
      
      $url_prefix = ( self::is_windows() ) ? 'file://localhost/' : 'file://localhost';
      
      //recombine file into URI and prepend protocol info
      return $url_prefix . join( '/', $accumulator );
    }
    return false;
  }

  /**
  * Decode an iTunes formatted URL string into an OS filesystem name
  * @param itunes_url str: the input iTunes URL to decode
  * @return           str: the OS style filename to pass to php functions or false
  */
  public static function itunes_format_decode( $itunes_url )
  {
    //build the iTunes URL prefix
    $url_prefix = ( self::is_windows() ) ? 'file://localhost/' : 'file://localhost';
      
    //strip the prepended protocol information
    $filename = str_replace( $url_prefix, '', rawurldecode( $itunes_url ) );
    
    //url decode the result
    return $filename;
  }

  /**
   * Is php running on a windows machine?
   * @return    bool: true if windows platform
   */
  public static function is_windows()
  {
    if( sfConfig::get('sf_environment') === 'test' )
    {
      return false;
    }
    else
    {
      return ( strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ) ? true : false;
    }
  }
  
  /**
   * Modifies a string to remove al non ASCII characters and spaces.
   * From Snipplr http://snipplr.com/view.php?codeview&id=22741
   * @param text string: the string to slugify
   * @return the slugified string
   * @see http://snipplr.com/view.php?codeview&id=22741
   */
  public function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
 
    // trim
    $text = trim($text, '-');
 
    // transliterate
    if (function_exists('iconv'))
    {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
 
    // lowercase
    $text = strtolower($text);
 
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
 
    if (empty($text))
    {
        return false;
    }
 
    return $text;
  }
  
  /**
   * check if an item is in an array in a case insensitive manner -works on 
   * single dimension arrays only for making configs user case insensitive
   * @param needle   mixed: needle to find
   * @param haystack mixed: haystack to search
   * @return         bool: if in array, return true.
   */
  public function in_array_ci($needle, $haystack)
  {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
  }
  
  /**
   * Remove null terminations and whitespace from a string (UTF8 friendly)
   * 
   * @param text     str: the dirty string
   * @return         str: non printable sanitized string
   */
  public function xmlize_uf8_string( $text )
  {
    $blacklist = array( chr(0), '\0', '\t', '\r', '\n', 'ÿþ' );
    foreach( range( chr(0),chr(127) ) as $alpha ) array_unshift( $blacklist, sprintf( '%sÿþ', $alpha ) );
    return  trim( str_replace( $blacklist, '', $text ) );
  }
}
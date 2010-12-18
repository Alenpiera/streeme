<?php

class scanartTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('source', null, sfCommandOption::PARAMETER_REQUIRED, 'The source to scan'),
      // add your own options here
    ));

    $this->namespace        = '';
    $this->name             = 'scan-art';
    $this->briefDescription = 'Scan various sources for album art';
    $this->detailedDescription = <<<EOF
The [scan-art|INFO] will initiate a scan of your albums for art from sources 
like Amazon PAS, your music files and artwork from the commons. Stores found 
images in sfproject/web/images/album_art


Sources:
  --source=amazon       - Use Amazon PAS (see cloudfusion config)
  --source=meta         - Read artwork embedded in your media
  --source=folders      - Read artwork from the media folders
  --source=service      - Read from future service (unused)

Call it with:

  [php symfony scan-art --source="..."|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    //bootstrap "client" context
    require_once( dirname(__FILE__) . '/../../config/ProjectConfiguration.class.php' );
    $configuration = ProjectConfiguration::getApplicationConfiguration( 'client', 'dev', true );
    sfContext::createInstance( $configuration );    
    
    // load the scanner 
    switch ( $options[ 'source' ] )
    {
      case 'amazon':
        require_once( dirname( __FILE__ ) . '/scanners/artworkScanAmazon.php' );
        break;
        
      case 'meta':
        require_once( dirname( __FILE__ ) . '/scanners/artworkScanMeta.php' );
        break;        
      
      case 'folders':
        require_once( dirname( __FILE__ ) . '/scanners/artworkScanFolders.php' );
        break;  
      
      case 'service':
        //reserved for future album art services
        throw new Exception( 'Not Ready: This is a reserved block for future album art sources.' );
        break; 
    }
    echo "\r\n";
  }
}
?>

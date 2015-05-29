<?php
require_once('class.exodFolder.php');
require_once('class.exodFile.php');
require_once('class.exodItemCache.php');

/**
 * Class exodItemFactory
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodItemFactory {

	/**
	 * @param stdClass $response
	 *
	 * @return exodFolder[]|exodFile[]
	 */
	public static function getInstancesFromResponse($response) {
		$return = array();
		if (count($response->value) == 0 OR ! $response instanceof stdClass) {
			return $return;
		}

		foreach ($response->value as $item) {
			if ($item->type == 'Folder') {
				$exid_item = new exodFolder();
				$exid_item->loadFromStdClass($item);
				exodItemCache::store($exid_item);
				$return[] = $exid_item;
			} else {
				$exid_item = new exodFile();
				$exid_item->loadFromStdClass($item);
				exodItemCache::store($exid_item);
				$return[] = $exid_item;
			}
		}

		return $return;
		/*
		 *
		 * object(stdClass)[119]
  public '@odata.type' => string '#Microsoft.FileServices.Folder' (length=30)
  public '@odata.id' => string 'https://phzh-my.sharepoint.com/_api/v1.0/me/files/01PPD3CZYWNTU3CGYFAFC2I7TIOSTHB7Q5' (length=84)
  public '@odata.etag' => string '"{B1E96C16-051B-4501-A47E-6874A670FE1D},1"' (length=42)
  public '@odata.editLink' => string 'me/files/01PPD3CZYWNTU3CGYFAFC2I7TIOSTHB7Q5' (length=43)
  public 'createdBy' =>
    object(stdClass)[123]
      public 'application' => null
      public 'user' =>
        object(stdClass)[124]
          public 'id' => string 'c8b97ed2-b7f5-4dd5-b765-3e1a10023b7c' (length=36)
          public 'displayName' => string 'Fabian Schmid,#i:0#.f|membership|extphzh.fschmid@phzh.ch,#extphzh.fschmid@phzh.ch,#,#Fabian Schmid' (length=98)
  public 'eTag' => string '"{B1E96C16-051B-4501-A47E-6874A670FE1D},1"' (length=42)
  public 'id' => string '01PPD3CZYWNTU3CGYFAFC2I7TIOSTHB7Q5' (length=34)
  public 'lastModifiedBy' =>
    object(stdClass)[125]
      public 'application' => null
      public 'user' =>
        object(stdClass)[126]
          public 'id' => string 'c8b97ed2-b7f5-4dd5-b765-3e1a10023b7c' (length=36)
          public 'displayName' => string 'Fabian Schmid,#i:0#.f|membership|extphzh.fschmid@phzh.ch,#extphzh.fschmid@phzh.ch,#,#Fabian Schmid' (length=98)
  public 'name' => string 'Shared with Everyone' (length=20)
  public 'parentReference' =>
    object(stdClass)[127]
      public 'driveId' => string '01IQTDW5ZH4WY7I2P74ZBIFANGH363C7BW' (length=34)
      public 'id' => string '01PPD3CZ56Y2GOVW7725BZO354PWSELRRZ' (length=34)
      public 'path' => string '/' (length=1)
  public 'size' => int 0
  public 'dateTimeCreated' => string '2014-03-05T15:42:12Z' (length=20)
  public 'dateTimeLastModified' => string '2014-03-05T15:42:12Z' (length=20)
  public 'type' => string 'Folder' (length=6)
  public 'webUrl' => string 'https://phzh-my.sharepoint.com/personal/extphzh_fschmid_phzh_ch/Documents/Shared%20with%20Everyone' (length=98)
  public 'childCount' => int 0
object(stdClass)[128]
  public '@odata.type' => string '#Microsoft.FileServices.Folder' (length=30)
  public '@odata.id' => string 'https://phzh-my.sharepoint.com/_api/v1.0/me/files/01PPD3CZ4QMMTYI2QHRVGJRJRL75VIUIK6' (length=84)
  public '@odata.etag' => string '"{84276390-076A-4C8D-98A6-2BFF6A8A215E},1"' (length=42)
  public '@odata.editLink' => string 'me/files/01PPD3CZ4QMMTYI2QHRVGJRJRL75VIUIK6' (length=43)
  public 'createdBy' =>
    object(stdClass)[129]
      public 'application' => null
      public 'user' =>
        object(stdClass)[130]
          public 'id' => string 'c8b97ed2-b7f5-4dd5-b765-3e1a10023b7c' (length=36)
          public 'displayName' => string 'Fabian Schmid,#i:0#.f|membership|extphzh.fschmid@phzh.ch,#extphzh.fschmid@phzh.ch,#,#Fabian Schmid' (length=98)
  public 'eTag' => string '"{84276390-076A-4C8D-98A6-2BFF6A8A215E},1"' (length=42)
  public 'id' => string '01PPD3CZ4QMMTYI2QHRVGJRJRL75VIUIK6' (length=34)
  public 'lastModifiedBy' =>
    object(stdClass)[131]
      public 'application' => null
      public 'user' =>
        object(stdClass)[132]
          public 'id' => string 'c8b97ed2-b7f5-4dd5-b765-3e1a10023b7c' (length=36)
          public 'displayName' => string 'Fabian Schmid,#i:0#.f|membership|extphzh.fschmid@phzh.ch,#extphzh.fschmid@phzh.ch,#,#Fabian Schmid' (length=98)
  public 'name' => string 'Testordner' (length=10)
  public 'parentReference' =>
    object(stdClass)[133]
      public 'driveId' => string '01IQTDW5ZH4WY7I2P74ZBIFANGH363C7BW' (length=34)
      public 'id' => string '01PPD3CZ56Y2GOVW7725BZO354PWSELRRZ' (length=34)
      public 'path' => string '/' (length=1)
  public 'size' => int 0
  public 'dateTimeCreated' => string '2014-12-15T12:55:18Z' (length=20)
  public 'dateTimeLastModified' => string '2014-12-15T12:55:33Z' (length=20)
  public 'type' => string 'Folder' (length=6)
  public 'webUrl' => string 'https://phzh-my.sharepoint.com/personal/extphzh_fschmid_phzh_ch/Documents/Testordner' (length=84)
  public 'childCount' => int 1
		 *
		 *
		 */
	}
}

?>

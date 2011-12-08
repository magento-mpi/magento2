/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
package varien.upload
{
	import flash.errors.IOError;
	import flash.errors.IllegalOperationError;
	import flash.events.Event;
	import flash.net.FileReference;
	
	import mx.core.IMXMLObject;
	
	/**	 
	 *  @eventType varien.upload.UploaderEvent.SELECT
	 */
	[Event(name='select', type='varien.upload.UploaderEvent')]
	
	public class UploaderSingle extends Uploader implements IMXMLObject
	{
		
		protected var _file:FileReference;
		
		/**
		 * Constructor
		 * 
		 * @param config configuration of uploader
		 */
		public function UploaderSingle(config:Object=null)
		{
			super(config);
			_file	= new FileReference();
			_file.addEventListener(Event.SELECT, _handleSelect);
		}
		
		/**
		 * Browse files for upload
		 */
		override public function browse():void
		{
			_file.browse(useTypeFilter ? getTypeFiltersArray() : null);
		}
		
		override protected function _handleSelect(event:Event):void
		{			
			var badFile:Boolean = false;
			var zeroSize:Boolean = false;				 
			try {
				_file.size;
			} 
			catch (exception:IllegalOperationError) {
				badFile = true;
			}
			catch (exception:IOError) { // If file size == 0
				zeroSize = true;
			}
			
			if(!badFile) {
				var id:String = _uniqueFileId(_file);
				_files[id] = {status:Uploader.FILE_NEW, file:_file, uploadTry:0};
				if(zeroSize) {
					_files[id].status 	  = Uploader.FILE_ERROR;
					_files[id].errorCode  = Uploader.ERROR_ZERO_SIZE;
					_files[id].error	  = 'File size should be more than 0 bytes';
				}
			}
			_createEvent(UploaderEvent.SELECT);
		}

	}
	
}

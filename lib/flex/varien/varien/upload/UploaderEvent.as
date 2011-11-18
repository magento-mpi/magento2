/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
package varien.upload
{
	import flash.events.Event;

	public class UploaderEvent extends Event
	{		
		/**
		 * @eventType progress
		 */
		public static const PROGRESS:String 	= 'progress';
		
		/**
		 * @eventType error
		 */
		public static const ERROR:String 		= 'error';

		/**
		 * @eventType select
		 */
		public static const SELECT:String 		= 'select';
		
		/**
		 * @eventType complete
		 */
		public static const COMPLETE:String 	= 'complete';
		
		/**
		 * @eventType cancel
		 */
		public static const CANCEL:String 		= 'cancel';
		
		/**
		 * @eventType remove
		 */
		public static const REMOVE:String 		= 'remove';

		/**
		 * @eventType removeall
		 */
		public static const REMOVE_ALL:String 		= 'removeall';

		protected var _data:Object;
		
		public function UploaderEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);		

		}
		
		public function get data():Object
		{
			return _data;
		}
		
		public function set data(value:Object):void
		{
			_data = value;			
		}
	}
}
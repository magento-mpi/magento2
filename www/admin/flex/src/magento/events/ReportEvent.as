package magento.events
{
	import flash.events.Event;

	public class ReportEvent extends Event
	{
		public static const CONFIG_LOADED:String = "configurationLoaded"; // When constructor configuration loaded
		
		public function ReportEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
		
	}
}
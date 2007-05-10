package magento.events
{
	import flash.events.Event;

	public class ConstructorEvent extends Event
	{
		public static const DATA_CHANGE:String = "constructorDataChanged";
		
		public function ConstructorEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
		}
		
	}
}
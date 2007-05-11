package magento.core
{
	/**
	 * Component for storing of configuration data
	 */
	 
	public class Config
	{
		private static var configuration:Object = new Object();
		
		public static function load(data:Object):void
		{
			for(var i:String in data)
				configuration[i] = data[i];
		}
		
		public static function getVar( name:String ):*
		{
			return configuration[name];
		}
		
		public static function setVar( name:String, data:* ):void
		{
			configuration[name] = data;
		}
		
	}
}
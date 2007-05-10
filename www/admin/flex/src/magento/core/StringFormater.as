package magento.core
{
	import mx.utils.ObjectUtil;
	
	/**
	 * Component for formating strings with 
	 * constructions like {property.property.property}
	 */
	public class StringFormater
	{
		private static const constructionPattern:RegExp = new RegExp("\\{[a-zA-Z][a-zA-Z0-9\\._]+\\}","g");
		
		private static const leftSkip:Number  = 1;
		private static const rightSkip:Number = -1;
		
		public static function format( string:String, replacements:Object ):String
		{
			var matches:Array = string.match(constructionPattern);
			
			if(matches)
			{
				for each(var match:String in matches)
				{
					var propertiesRawPath:String = match.substr(leftSkip).substr(0,rightSkip);
					
					var propertiesPath:Array = propertiesRawPath.split('.');
					var parentObject:Object = ObjectUtil.copy(replacements);
					var property:String = null;
					
					for(var i:Number = 0; i < propertiesPath.length; i++)
					{
						if(parentObject[propertiesPath[i]]!=null && typeof(parentObject[propertiesPath[i]])=='object')
							parentObject = parentObject[propertiesPath[i]];
							
						else if(parentObject[propertiesPath[i]]!=null && i+1==propertiesPath.length)
							property     = parentObject[propertiesPath[i]];
							
						else
							break;
					}
					
					string = string.replace(match, property);
				}
			}
			
			return string;
		}
	}
}
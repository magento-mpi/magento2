package magento.constructor
{
	import mx.containers.Panel;
	import magento.events.ConstructorEvent;

	public class ChartPanel extends Panel
	{
		private var _dataProvider:Object = null;
		
		public function ChartPanel()
		{
			super();
			addEventListener( ConstructorEvent.DATA_CHANGE , renderGlobal );
		}
		
		public function set dataProvider(value:Object):void
		{
			_dataProvider = value;
			dispatchEvent( new ConstructorEvent( ConstructorEvent.DATA_CHANGE ) );
		}
		
		[Bindable(event='constructorDataChanged')]
		public function get dataProvider():Object
		{
			return _dataProvider;
		}
		
		public function renderGlobal( event:ConstructorEvent ):void
		{
			title = dataProvider.title;
			if( dataProvider.width )
					if( String(dataProvider.width).match(/^[0-9]+%$/) )
						percentWidth = parseInt( dataProvider.width );
					else
						width = parseInt( dataProvider.width );
				
			if( dataProvider.height )
				if( String(dataProvider.height).match(/^[0-9]+%$/) )
					percentHeight = parseInt( dataProvider.height );
				else
					height = parseInt( dataProvider.height );
			
			if( dataProvider.minWidth )
				minWidth = parseInt( dataProvider.minWidth );
			
			if( dataProvider.minHeight )
				minHeight = parseInt( dataProvider.minHeight );
		}
	}
}
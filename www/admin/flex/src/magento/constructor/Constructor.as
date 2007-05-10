package magento.constructor
{
	import mx.containers.VBox;
	import mx.collections.ICollectionView;
	import magento.events.ConstructorEvent;

	/**
	 *  TODO: Implement creating component by config 
	 */
	public class Constructor extends VBox
	{
		private var _dataProvider:XML = null;
		
		
		public function Constructor()
		{
			super();
		}
		
		public function set dataProvider(value:XML):void
		{
			_dataProvider = value;
			dispatchEvent( new ConstructorEvent( ConstructorEvent.DATA_CHANGE ) );
		}
		
		[Bindable(event='constructorDataChanged')]
		public function get dataProvider():XML
		{
			return _dataProvider;
		}
	}
}
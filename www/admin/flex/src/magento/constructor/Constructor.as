package magento.constructor
{
	import mx.containers.VBox;
	import magento.events.ConstructorEvent;
	import mx.collections.ArrayCollection;
	import mx.containers.Box;
	import mx.core.UIComponent;

	/**
	 *  TODO: Implement creating component by config 
	 */
	public class Constructor extends VBox implements IConstructor
	{
		private var _dataProvider:Object = null;
		private var chartPanels:Object;
		
		public function Constructor()
		{
			super();
			this.addEventListener( ConstructorEvent.DATA_CHANGE, renderGlobal );
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
		
		private function renderGlobal( event:ConstructorEvent ):void
		{
			chartPanels = new Array();
			removeAllChildren();
			renderBoxes( dataProvider , this );
		}
		
		private function renderBoxes( data:Object, parent:UIComponent ):void
		{
			var boxes:Array = new Array();
			
			if(data.box && data.box is ArrayCollection)
				boxes = data.box.toArray();
			else if(data.box)
				boxes.push(data.box);
			
			for(var i:String in boxes)
			{
				var box:Box = new Box();
				box.direction = boxes[i].direction;
				box.styleName = "constructorBox";
				box.percentWidth = 100;
				box.percentHeight = 100;
				parent.addChild(box);
				if(boxes[i].box)
					renderBoxes(boxes[i], box);
				else if(boxes[i].chartpanel)
					renderChartpanels( boxes[i], box );
			}
		}
		
		private function renderChartpanels( data:Object, parent:UIComponent ):void
		{
			var panels:Array = new Array();
			
			if(data.chartpanel is ArrayCollection)
				panels = data.chartpanel.toArray();
			else
				panels.push(data.chartpanel);
			
			for( var i:String in panels )
			{
				var panel:ChartPanel = new ChartPanel();
				
				parent.addChild( panel );
				panel.dataProvider = panels[i];
				chartPanels[panels[i].id] = panel;
			}
		}
	}
}
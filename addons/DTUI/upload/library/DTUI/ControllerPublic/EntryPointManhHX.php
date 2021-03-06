<?php
abstract class DTUI_ControllerPublic_EntryPointManhHX extends DTUI_ControllerPublic_EntryPointBase {
	public function actionNewOrder() {
		if ($this->_request->isPost()) {
			// this is a POST request
			// start creating new order
			$input = $this->_input->filter(array(
				'table_id' => XenForo_Input::UINT,
				'item_ids' => array(XenForo_Input::UINT, 'array' => true)
			));
			
			$orderDw = XenForo_DataWriter::create('DTUI_DataWriter_Order');// new order
			$orderDw->set('table_id', $input['table_id']);
			$orderDw->set('order_date',XenForo_Application::$time);
			$orderDw->save();// storage new order into Order table in database;
			
			$order = $orderDw->getMergedData();// get data is saved ;
			// create items for a order 
			foreach ($input['item_ids'] as $itemId) {
				$orderItemDw = XenForo_DataWriter::create('DTUI_DataWriter_OrderItem');// a item in a order
				$orderItemDw->set('order_id', $order['order_id']);
				$orderItemDw->set('trigger_user_id',XenForo_Visitor::getUserId());
				$orderItemDw->set('target_user_id',0);
				$orderItemDw->set('item_id',$itemId);//
				$orderItemDw->set('order_item_date', XenForo_Application::$time);
				$orderItemDw->set('status','waiting');
				
				$tmp = $orderItemDw->save();// storage new order_item into OrderItem table in Database
			}
			
			die('ok');
		} else {
			// this is a GET request
			// display a form
			$tables = $this ->_getTableModel()->getAllTable();// get array tables with key= tableId and values = tableName
			$items = $this->_getItemModel()->getAllItem();// get all items from database
			
			$viewParams = array(
			'table' => $tables,
			'items' => $items
			);
			
			return $this -> responseView('DTUI_ViewPublic_EntryPoint_NewOrder','dtui_entrypoint_new_order',$viewParams);
		}
	}
	
	public function actionTasks(){// return list order_items of a user 
		$userIdtmp = XenForo_Visitor::getUserId();
		$conditions = array('userId' => $userIdtmp);
		$order_items = $this->_getOrderItemModel()->getAllOrderItem($conditions);
		
		$viewParams = array(
			'tasks' => $order_items
		);
		
		return $this -> responseView('DTUI_ViewPublic_EntryPoint_Tasks','dtui_task_list',$viewParams);
	}
	
	public function actionOrders(){// get all Order in database
		$orders = $this->_getOrderModel()->getAllOrder();
		
		$viewParams = array(
			'orders' => $orders
		);
		
		return $this -> responseView('DTUI_ViewPublic_EntryPoint_Orders','dtui_order_list',$viewParams);
	}
}
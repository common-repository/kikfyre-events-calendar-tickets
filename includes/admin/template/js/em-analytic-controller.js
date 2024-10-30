eventMagicApp.controller('analyticCtrl',function($scope, $http,MediaUploader,PostUtility,EMRequest){
    $scope.data={}; 
    $scope.requestInProgress= false;
    $scope.days= ["today","yesterday"];
    $scope.weeks= ["this_week","last_week","custom"];
    $scope.years= ["this_year","last_year"];
    $scope.months= ["this_month","last_month"];
    $scope.showFilterOptions= false;
    
    angular.element(document).ready(function () {
       google.charts.load('current', {'packages':['corechart']});
       jQuery("#start_date").datepicker({changeMonth: true,yearRange: "-100:+0", changeYear: true, maxDate: new Date});
       jQuery("#end_date").datepicker({changeMonth: true,yearRange: "-100:+0", changeYear: true, maxDate: new Date});
        //  google.charts.setOnLoadCallback($scope.drawChart);
    });
    
    $scope.progressStart= function()
    {
        $scope.requestInProgress = true;
    }
    
    $scope.progressStop= function()
    {
        $scope.requestInProgress = false;
    }
    
    $scope.drawChart= function(rows){
        
      var data = new google.visualization.DataTable();
      /* First Column Type */
      if($scope.days.indexOf($scope.data.report_by)>-1)
      {
          data.addColumn('timeofday', 'Time');
      }
      
      if($scope.weeks.indexOf($scope.data.report_by)>-1 || $scope.months.indexOf($scope.data.report_by)>-1)
      {
          data.addColumn('date', 'Day');
      }
      
      if($scope.years.indexOf($scope.data.report_by)>-1)
      {
          data.addColumn('string', 'Month');
      }
      /* Other Column Type */
      if($scope.data.filter_type=="booking")
        data.addColumn('number', 'Number of Bookings'); 
      else
         data.addColumn('number', 'Revenue($)');  
      console.log(rows);
      data.addRows(rows);  

      var options = {
        height: 450,
        timeline: {
          groupByRowLabel: true
        }
      };

      
        var chart = new google.visualization.LineChart(document.getElementById('revenue_chart'));

        chart.draw(data, options);
      
    }
    
    $scope.getData= function()
    {
        $scope.progressStart();
        EMRequest.send('em_load_chart',$scope.data).then(function(response){
            $scope.progressStop();
            angular.forEach(response.data,function(row,key){
             
                if($scope.weeks.indexOf($scope.data.report_by)>-1 || $scope.months.indexOf($scope.data.report_by)>-1)
                 row[0]= new Date(row[0]);
             //   row[1] = new Date(row[1]);
            });
            $scope.drawChart(response.data);
        });
    }
    
    $scope.initialize= function()
    {
        $scope.data.em_request_context= 'admin_analytics';
        $scope.progressStart();
         EMRequest.send('em_load_strings',$scope.data).then(function(response){
          $scope.progressStop();   
          $scope.data= response.data;
        });
    }
    
    $scope.showFilters= function()
    {
       if($scope.data.filter_type)
          $scope.showFilterOptions= true;
       else
           $scope.showFilterOptions= false;  
    }
    
});

package com.hdfc.logic;

import in.co.itracksolution.InsertVehicleAlerts;
import in.co.itracksolution.pull_full_data_cassandra;
import in.co.itracksolution.dao.DistanceLogDao;
import in.co.itracksolution.dao.FullDataDao;
import in.co.itracksolution.dao.NightLogDao;
import in.co.itracksolution.dao.SpeedAlertDao;
import in.co.itracksolution.dao.TravelLogDao;
import in.co.itracksolution.dao.TurnAlertDao;
import in.co.itracksolution.model.FullData;

import java.io.FileWriter;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.TimeZone;
import java.util.TreeMap;

import com.datastax.driver.core.ResultSet;
import com.datastax.driver.core.Row;
import com.datastax.driver.core.Session;
import com.hdfc.db.mysql.connection;
import com.hdfc.db.mysql.mysql_handler;
import com.hdfc.init.init;
import com.hdfc.report.report_night_movement;
import com.hdfc.utils.utility_class;
import com.iespl.gisgraphy.LatLng;
import com.iespl.gisgraphy.class_pop_road;

public class worker {
	
	public static ArrayList<Integer> vehicle_id = new ArrayList<Integer>();
	public static ArrayList<String> vehicle_name = new ArrayList<String>();
	public static ArrayList<Float> max_speed = new ArrayList<Float>();
	public static ArrayList<String> device_imei_no = new ArrayList<String>();
	public static SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");	
	public static TimeZone tz = TimeZone.getTimeZone("Asia/Kolkata");	
	
	//SampleFullDataQuery st = new SampleFullDataQuery();
	public static pull_full_data_cassandra fd = new pull_full_data_cassandra();
	public static FullData data = new FullData();			
	public static FullDataDao dao = new FullDataDao(fd.conn.getSession());
	
	public static InsertVehicleAlerts st = new InsertVehicleAlerts();
	public static Session session = st.conn.getSession();	
	
	public static String previous_date1 ="", previous_date2 ="";
	public static double interval=3600.0; //in secs : 1 hour 
	
	//####### TEMPORARY FILE WRITE
	public static FileWriter fw = null;
	public static String dtime ="", stime ="", latitude="", longitude="", roadId="", roadName="", locationId="", locationName="", q=",", line="";
	public static float speed =0.0f, angle =0.0f;	
	//##############################

	public worker() {
		//sdf.setTimeZone(tz);
	}
	
	public static void process_data(int account_id) {
		
		//###### MYSQL CONN
		connection sql_con = new connection();
		//mysql_handler mh = new mysql_handler();
		mysql_handler.getVehicleInformation(sql_con, account_id);
		
		sdf.setTimeZone(tz);
		//System.out.println("AftergetVehicleInfo");
		String day_before_previous_day = utility_class.getDayBeforeYesterdayDateString();
		String previous_day = utility_class.getYesterdayDateString();
		previous_date1 = day_before_previous_day+" 23:00:00";
		previous_date2 = previous_day+" 04:00:00";

		//System.out.println("p1="+previous_date1+" ,p2="+previous_date2);
		previous_date1 = "2015-06-01 00:00:00";
		previous_date2 = "2015-07-31 23:59:59";
	//	previous_date1 = "2015-07-29 00:00:00";
	//	previous_date2 = "2015-07-31 23:59:59";		
				
		System.out.println("SizeIMEI="+init.device_imei_no.size());
		for(int i=0;i<(init.device_imei_no.size());i++) {			
			
			//####### TEMPORARY
			//report_distance.set_variables();
			report_night_movement.set_variables();
			//report_speed_violation.set_variables();
			//####### RESET ARRAY LISTS -TURN
			
			//####### RESET ARRAY LISTS -TRAVEL VIOLATION
			report_night_movement.IMEI_No = new ArrayList<String>();    
			report_night_movement.ServerTime = new ArrayList<String>();
			report_night_movement.AvgSpeed = new ArrayList<Double>();
			report_night_movement.Distance = new ArrayList<Double>();
			report_night_movement.MaxSpeed = new ArrayList<Double>();
			report_night_movement.StartDeviceTime = new ArrayList<String>();
			report_night_movement.EndDeviceTime = new ArrayList<String>();
			report_night_movement.StartLatitude = new ArrayList<Double>();
			report_night_movement.StartLongitude = new ArrayList<Double>();
			report_night_movement.StartlatLngObj = new ArrayList<LatLng>();
			report_night_movement.StartlocationId = new ArrayList<String>();
			report_night_movement.Startlocation = new ArrayList<String>();
			report_night_movement.EndLatitude = new ArrayList<Double>();
			report_night_movement.EndLongitude = new ArrayList<Double>();
			report_night_movement.EndlatLngObj = new ArrayList<LatLng>();
			report_night_movement.EndlocationId = new ArrayList<String>();
			report_night_movement.Endlocation = new ArrayList<String>(); 
			report_night_movement.TravelDuration = new ArrayList<Integer>();
			
			//System.out.println("Device="+init.device_imei_no.get(i));
			pull_and_process_data(init.vehicle_name.get(i), init.max_speed.get(i), init.device_imei_no.get(i), previous_date1, previous_date2);
			System.out.println("Pullprocess completed..");
			//### PUSH ::DISTANCE REPORT :: ARRAYLIST TO CASSANDRA
			push_vehicle_night_travel_to_database(init.device_imei_no.get(i), session);
//			push_chauraha_to_database(init.device_imei_no.get(i), session);
//			System.out.println("Processed IMEI:"+init.device_imei_no.get(i)+" -"+i);
		}
		
		fd.close();
		st.close();
		//System.out.println("Point3");
	}
	
	public static void pull_and_process_data(String vehicle_name, Float max_speed, String imei, String startDateTime, String endDateTime) {				
		
		String device_time ="", sts ="", lat_str ="", lng_str ="";
		double lat=0.0, lng=0.0, speed =0.0;

		Float ax = 0.0f, ay = 0.0f, az =0.0f,	bx=0.0f, by=0.0f, bz=0.0f, cx=0.0f, cy=0.0f, cz=0.0f;
		//report_distance rep_distance = new report_distance();
		report_night_movement rep_travel = new report_night_movement();
		//System.out.println("Point2");
		//data.setImei("862170011627815"); //Make sure this imei exists
		//data.setDate("2015-01-29");
		//ResultSet rs= dao.selectByImeiAndDate(data.getImei(), data.getDate());	
		/*String imei = "359231030125239";
		String startDateTime = "2015-01-01 10:00:00";
		String endDateTime = "2015-01-01 15:00:00";*/
				
		Boolean deviceTime = true;	// true for device time index, otherwise server time
		Boolean orderAsc = true;	// true for ascending , otherwise descending (default) 

		//System.out.println("IMEI="+imei+" ,StartDate="+startDateTime+" ,EndDate="+endDateTime+" ,DeiveTime="+deviceTime);
		ArrayList<FullData> fullDataList = dao.selectByImeiAndDateTimeSlice(imei, startDateTime, endDateTime, deviceTime, orderAsc);

		String tmp_lat ="", tmp_lng="";
		int data_size = fullDataList.size();		
		int record_count =1;
		System.out.println("DataSize="+data_size);
		
		for (FullData fullData : fullDataList)
		{
			TreeMap pMap1 = new TreeMap();
			pMap1 = fullData.getPMap();
			/*System.out.print("imei: "+fullData.getImei()+" ");
			System.out.print("device time: "+sdf.format(fullData.getDTime())+" ");
			System.out.print("server time: "+sdf.format(fullData.getSTime())+" ");
			System.out.print("a: "+pMap1.get("a")+" ");
			System.out.print("b: "+pMap1.get("b")+" ");
			System.out.print("c: "+pMap1.get("c")+" ");
			System.out.print("d: "+pMap1.get("d")+" ");
			System.out.print("e: "+pMap1.get("e")+" ");
			System.out.print("f: "+pMap1.get("f")+" ");
			System.out.println();*/
				
			//System.out.println("Lat="+pMap1.get("d")+" ,Lng="+pMap1.get("e"));
			tmp_lat = (String) pMap1.get("d");
			tmp_lng = (String) pMap1.get("e");
			
			//System.out.println("Lat="+tmp_lat+" ,Lng="+tmp_lng);
			if( (!tmp_lat.equals("")) && (!tmp_lng.equals("")) ) {
					
				/*System.out.print("imei: "+fullData.getImei()+" ");
				System.out.print("device time: "+sdf.format(fullData.getDTime())+" ");
				System.out.print("server time: "+sdf.format(fullData.getSTime())+" ");
				System.out.print("a: "+pMap1.get("a")+" ");
				System.out.print("b: "+pMap1.get("b")+" ");
				System.out.print("c: "+pMap1.get("c")+" ");
				System.out.print("d: "+pMap1.get("d")+" ");
				System.out.print("e: "+pMap1.get("e")+" ");
				System.out.print("f: "+pMap1.get("f")+" ");
				System.out.println();*/	
				
				//System.out.println("device time: "+sdf.format(fullData.getDTime())+" ,lat="+tmp_lat+" ,tmp_lng="+tmp_lng);
				device_time = sdf.format(fullData.getDTime());
				sts = sdf.format(fullData.getSTime());
				tmp_lat = tmp_lat.substring(0,tmp_lat.length()-1);
				tmp_lng = tmp_lng.substring(0,tmp_lng.length()-1);
				lat = Double.parseDouble(tmp_lat);
				lng = Double.parseDouble(tmp_lng);
				speed = Double.parseDouble((String) pMap1.get("f"));
				//CHECK ALERTS
				CHECK_ALERTS(imei, startDateTime, endDateTime, interval, device_time, sts, lat, lng, speed, max_speed, data_size, record_count, rep_travel);
				record_count++;
			}
		}
	}

	
	public static void CHECK_ALERTS(String imei, String startdate, String enddate, double interval, String device_time, String sts, double lat, double lng, double speed, Float max_speed, int data_size, int record_count, report_night_movement rep_travel) {
		//CHECK AND PUSH
		//report_distance.action_report_distance(imei, device_time, sts, startdate, enddate, interval, lat, lng, speed, data_size, record_count);
		report_night_movement.action_report_travel(imei, device_time, sts, startdate, enddate, interval, lat, lng, speed, data_size, record_count);
//		report_turning_violation.action_report_truning_violation(imei, device_time, sts, startdate, enddate, interval, lat, lng, speed, data_size, record_count);
//		report_travel.action_report_travel(imei, device_time, sts, startdate, enddate, interval, lat, lng, speed, data_size, record_count);
		
	}
	
	//######## VEHICLE TRAVEL -PUSH 
	public static void push_vehicle_night_travel_to_database(String imei, Session session) {
		
		String starttime="",endtime="", startlatitude="",startlongitude="",endlatitude="",endlongitude="";
		String startlocationid="",endlocationid="",startlocation="",startlocationname="",endlocationname="";
		float distance=0.0f,avgspeed=0.0f,maxspeed=0.0f;
		int travel_duration=0;
		
		//########## GIS -FOR location
		/*int rad=200;//meter
		String tmp_loc ="";
		System.out.println("GIS Data Before="+report_travel.StartlatLngObj.size());
		class_pop_road rd_lat_lng= new class_pop_road(report_travel.StartlatLngObj,rad);		
		
		ArrayList<LatLng>  data = rd_lat_lng.getLatlngData();
		
		System.out.println("GIS Data AfterRequest="+data.size());
		
		for(LatLng obj1 : data){
			try{
				//System.out.println("Lat : "+obj1.getLat());
				//System.out.println("lng : "+obj1.getLng());			
				//System.out.println("locationCode : "+obj1.getLocationCode());
				//System.out.println("location : "+obj1.getLocation());
				//System.out.println("Distance : "+obj1.getDistance());
				//System.out.println("Is_in : "+obj1.getIs_in());
				
				report_turning_violation.roadID.add(obj1.getLocationCode());			
				if(!obj1.getDistance().equals("-")) {					
					if(obj1.getIs_in()!=null) {
						tmp_loc = Double.parseDouble(obj1.getDistance())/1000+" Km from "+obj1.getLocation()+","+obj1.getIs_in();
					} else {
						tmp_loc = Double.parseDouble(obj1.getDistance())/1000+" Km from "+obj1.getLocation();
					}
				} else {
					tmp_loc = "-";
				}
				//System.out.println("Loc : "+tmp_loc);
				report_turning_violation.roadName.add(tmp_loc);
				//report_turning_violation.roadID.add();
			} catch(Exception e1) {System.out.println("LocError="+e1.getMessage());}
		}
		
		System.out.println("GIS Data After="+report_turning_violation.roadName.size());*/
		//#############
		
		//String filename= "D:\\HDFC/hdfc_alert_report/night_driving/"+imei+".csv";
		String filename= "/mnt/hdfc_report/night_driving/"+imei+".csv";
		line = "ServerTime,StartTime,EndTime,TravelDuration(Mins),AvgSpeed(Km/hr),Distance(Km),MaxSpeed(Km/hr)\n";
		try {
			fw = new FileWriter(filename,true);
		} catch (IOException e3) {
			// TODO Auto-generated catch block
			e3.printStackTrace();
		} //the true will append the new data*/		
		
		//System.out.println("Size="+report_night_movement.IMEI_No.size());
		
		if(report_night_movement.IMEI_No.size() > 0) {
			
//			NightLogDao nightLogDao = new NightLogDao(session);		
			
			for(int i=0;i<report_night_movement.IMEI_No.size();i++) {
				
				starttime = report_night_movement.StartDeviceTime.get(i);
				endtime = report_night_movement.EndDeviceTime.get(i);
				stime = report_night_movement.ServerTime.get(i);
				distance = (float)((double)Double.valueOf(report_night_movement.Distance.get(i)));
				avgspeed = (float)((double)Double.valueOf(report_night_movement.AvgSpeed.get(i)));
				maxspeed = (float)((double)Double.valueOf(report_night_movement.MaxSpeed.get(i)));		

				//locationId = "";
				//locationName = "";
				startlatitude = report_night_movement.StartLatitude.get(i).toString();
				startlongitude = report_night_movement.StartLongitude.get(i).toString();
				endlatitude = report_night_movement.EndLatitude.get(i).toString();
				endlongitude = report_night_movement.EndLongitude.get(i).toString();				

				/*startlocationid = report_travel.StartlocationId.get(i);
				endlocationid = report_travel.EndlocationId.get(i);		
				startlocationname = report_travel.Startlocation.get(i);		
				endlocationname = report_travel.EndlocationId.get(i);*/				
				travel_duration = report_night_movement.TravelDuration.get(i);
				
				//line += tDeviceTime+q+tServerTime+q+tSpeed+q+tAngle+q+tLatitude+q+tLongitude+"\n";
				line += stime+","+starttime+","+endtime+","+travel_duration+","+avgspeed+","+distance+","+maxspeed+"\n";
//				nightLogDao.insertNightLog(imei, starttime, startlatitude, startlongitude, startlocationid, startlocationname, endtime, endlatitude, endlongitude, endlocationid, endlocationname, travel_duration, avgspeed, distance, maxspeed);
				
				//System.out.println("line="+line);
			}
		    
			try {
				fw.write(line);
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}//appends the string to the file
		}
	    
		System.out.println("VehicleTravel Push-ok");
		
	    try {
			fw.close();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}	
	
}

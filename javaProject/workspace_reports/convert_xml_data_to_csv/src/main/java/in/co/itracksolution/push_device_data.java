package in.co.itracksolution;

import in.co.itracksolution.dao.FullDataDao;
import in.co.itracksolution.dao.LastDataDao;
import in.co.itracksolution.db.CassandraConn;
import in.co.itracksolution.model.FullData;
import in.co.itracksolution.model.LastData;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.text.SimpleDateFormat;
import java.lang.String;
import java.util.Date;
import java.util.TimeZone;
import java.util.Calendar;
import java.util.Properties;

public class push_device_data {
	public static CassandraConn conn;
	public FullDataDao ops = null;
	
	public push_device_data(){
		String propFileName = "config.properties";
		Properties prop = new Properties();
		
		try {
			InputStream inputStream = getClass().getClassLoader().getResourceAsStream(propFileName);
		
			if (inputStream != null) {
				prop.load(inputStream);
				conn = new CassandraConn(prop.getProperty("nodes"), prop.getProperty("keyspace"), prop.getProperty("username"), prop.getProperty("password"));
			
			} else {
				throw new FileNotFoundException("property file '" + propFileName + "' not found in the classpath");
			}
			
			ops = new FullDataDao(conn.getSession());
					
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
	
	public static void close(){
		if (conn !=null)
			conn.close();
	}

	public void insertFulldata(String imei, String dtime, String data){
		TimeZone IST = TimeZone.getTimeZone("Asia/Kolkata");
		Calendar now = Calendar.getInstance(IST); //gets a calendar using time zone and locale
		now.setTimeZone(IST);
		
		String date = dtime.substring(0,10);
		SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
	
		Date dtObj = new Date();	
		try { 
			dtObj = sdf.parse(dtime);
		}
		catch (Exception e) {
			e.printStackTrace();
		}
	
		FullData fullData = new FullData(imei, date, dtObj, data, now.getTime());
		//FullDataDao ops = new FullDataDao(conn.getSession());
		
		System.out.println("Inserting Full Data with imei: "+imei);

		ops.insert(fullData);
		
	}
	
	public void insertLastdata(String imei, String data){
		TimeZone IST = TimeZone.getTimeZone("Asia/Kolkata");
		Calendar now = Calendar.getInstance(IST); // gets a calendar using the default time zone and locale.
		now.setTimeZone(IST);
	
		LastData lastData = new LastData(imei, now.getTime(), data);
		LastDataDao lastDao = new LastDataDao(conn.getSession());
		
		System.out.println("Inserting Last Data with imei: "+imei);
		lastDao.insert(lastData);
		
	}
	
	/*public static void main(String[] args) {
		
		SampleInsert st = new SampleInsert();
		
		// Full Data ('a','b','c','d','e','f','g','i','j','k','l','m','n','o','p','q','r','ci','ax','ay','az','mx','my','mz','bx','by','bz'); 
		String imei = "862170011627815";

		String dtime = "2015-01-29 04:57:19";
		String fullData = "N;v1.45C;1;26.25148;79.86157;0.06;2;5;3;5;6;6;3;5;0;12.88;abcd;1;0;0;1;0;0;1;0;0";
		st.insertFulldata(imei, dtime, fullData);
	
	
		// Last Seen Data ('a','b','c','d','e','f','g','i','j','k','l','m','n','o','p','q','r','s','t','u','ci','ax','ay','az','mx','my','mz','bx','by','bz');
		String lastSeenData = "N;v1.45C;1;26.25858;79.82557;0.06;2015-01-22@00:00:09;2;5;3;5;6;6;3;5;0;12.88;21;13:09:10;20:20:20;abcd;1;0;0;1;0;0;1;0;0";
		st.insertLastdata(imei, lastSeenData);
		
		st.close();	
	}*/
		
	
}

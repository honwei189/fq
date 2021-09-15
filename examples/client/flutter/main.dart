// description       : An example to show how to connect FQ API
// version           : "1.0.0"
// creator           : Gordon Lim <honwei189@gmail.com>
// created           : 08/10/2019 19:12:51
// last modified     : 
// last modified by  : Gordon Lim <honwei189@gmail.com>

import 'package:flutter/material.dart';
import 'dart:async';
import 'dart:convert';
// import 'dart:io';
import 'package:http/http.dart' as http;

void main() => runApp(MyApp());

class MyApp extends StatelessWidget {
  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Flutter Demo',
      theme: ThemeData(
        // This is the theme of your application.
        //
        // Try running your application with "flutter run". You'll see the
        // application has a blue toolbar. Then, without quitting the app, try
        // changing the primarySwatch below to Colors.green and then invoke
        // "hot reload" (press "r" in the console where you ran "flutter run",
        // or simply save your changes to "hot reload" in a Flutter IDE).
        // Notice that the counter didn't reset back to zero; the application
        // is not restarted.
        primarySwatch: Colors.blue,
      ),
      home: MyHomePage(title: 'Test'),
    );
  }
}

class MyHomePage extends StatefulWidget {
  MyHomePage({Key key, this.title}) : super(key: key);

  // This widget is the home page of your application. It is stateful, meaning
  // that it has a State object (defined below) that contains fields that affect
  // how it looks.

  // This class is the configuration for the state. It holds the values (in this
  // case the title) provided by the parent (in this case the App widget) and
  // used by the build method of the State. Fields in a Widget subclass are
  // always marked "final".

  final String title;

  @override
  _MyHomePageState createState() => _MyHomePageState();
}

class Login {
  int id;
  String name;
  List<String> locations;
  double price;
  int stock;
  bool active;

  Login(
      {this.id,
      this.name,
      this.locations,
      this.price,
      this.stock,
      this.active});
  Map<String, dynamic> toJson() => _loginToJson(this);
}

Data dataFromJson(String str) => Data.fromJson(json.decode(str));

String dataToJson(Data data) => json.encode(data.toJson());

class Data {
  UserUser userUser;

  Data({
    this.userUser,
  });

  factory Data.fromJson(Map<String, dynamic> json) => Data(
        userUser: json["user/user"] == null
            ? null
            : UserUser.fromJson(json["user/user"]),
      );

  Map<String, dynamic> toJson() => {
        "user/user": userUser == null ? null : userUser.toJson(),
      };
}

class UserUser {
  String test;

  UserUser({
    this.test,
  });

  factory UserUser.fromJson(Map<String, dynamic> json) => UserUser(
        test: json["test"] == null ? null : json["test"],
      );

  Map<String, dynamic> toJson() => {
        "test": test == null ? null : test,
      };
}

Future<http.Response> postRequest() async {
  var url = 'http://192.168.1.154:8000';

  // Map data = {'apikey': '12345678901234567890'};

  //encode Map to JSON
  Map data = {
    'find': 'user/user',
    // 'exec': 'test',
    'select': 'name, gender',
    'data': {
      'id': 1,
      'name': "sdfsf",
    },
  };

  var body = json.encode(data);

  var response = await http.post(url,
      headers: {"Content-Type": "application/json"}, body: body);
  // print("${response.statusCode}");
  // print("${response.body}");

  // var jsonObject =
  //     (jsonDecode(response.body) as List).cast<Map<String, dynamic>>();
  // var response1 =
  //     jsonObject.map((e) => e == null ? null : DataJSON.fromJson(e));
  // for (var item in response1) {
  //   print(item.request);
  // }

  // Map data1 = jsonDecode(response.body);
  // List<dynamic> list = List();
  // list = data1["data"].map((data1) => new DataJSON.fromJson(data1)).toList();

  // for (int b = 0; b < list.length; b++) {
  //   print(list);
  // }

  final Map<String, dynamic> jSONdata = jsonDecode(response.body);

  jSONdata.forEach((String key, dynamic data1) {
    print(key);
    print(data1);
    // print(data1[0]["name"]);
    for(var i = 0;i<data1.length;i++){
      print(data1[i]["name"] + " - " + data1[i]["gender"]);
    }
    // print(data1[0]["name"] + " - " + data1[0]["gender"]);
  });

  return response;
}

class _MyHomePageState extends State<MyHomePage> {
  int _counter = 0;

  void _incrementCounter() {
    setState(() {
      // This call to setState tells the Flutter framework that something has
      // changed in this State, which causes it to rerun the build method below
      // so that the display can reflect the updated values. If we changed
      // _counter without calling setState(), then the build method would not be
      // called again, and so nothing would appear to happen.
      _counter++;
    });

    // Map<String, dynamic> itemJson = login.toJson();

    // print(itemJson['name']);
    // print(itemJson.toString());

    postRequest();
  }

  @override
  Widget build(BuildContext context) {
    // This method is rerun every time setState is called, for instance as done
    // by the _incrementCounter method above.
    //
    // The Flutter framework has been optimized to make rerunning build methods
    // fast, so that you can just rebuild anything that needs updating rather
    // than having to individually change instances of widgets.
    return Scaffold(
      appBar: AppBar(
        // Here we take the value from the MyHomePage object that was created by
        // the App.build method, and use it to set our appbar title.
        title: Text(widget.title),
      ),
      body: Center(
        // Center is a layout widget. It takes a single child and positions it
        // in the middle of the parent.
        child: Column(
          // Column is also layout widget. It takes a list of children and
          // arranges them vertically. By default, it sizes itself to fit its
          // children horizontally, and tries to be as tall as its parent.
          //
          // Invoke "debug painting" (press "p" in the console, choose the
          // "Toggle Debug Paint" action from the Flutter Inspector in Android
          // Studio, or the "Toggle Debug Paint" command in Visual Studio Code)
          // to see the wireframe for each widget.
          //
          // Column has various properties to control how it sizes itself and
          // how it positions its children. Here we use mainAxisAlignment to
          // center the children vertically; the main axis here is the vertical
          // axis because Columns are vertical (the cross axis would be
          // horizontal).
          mainAxisAlignment: MainAxisAlignment.center,
          children: <Widget>[
            Text(
              'You have pushed the button this many times:',
            ),
            Text(
              '$_counter',
              style: Theme.of(context).textTheme.display1,
            ),
          ],
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _incrementCounter,
        tooltip: 'Increment',
        child: Icon(Icons.add),
      ), // This trailing comma makes auto-formatting nicer for build methods.
    );
  }
}

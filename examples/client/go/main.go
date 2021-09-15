// description       : An example to show how to connect FQ API
// version           : "1.0.0"
// creator           : Gordon Lim <honwei189@gmail.com>
// created           : 08/10/2019 19:23:35
// last modified     : 06/01/2020 19:46:09
// last modified by  : Gordon Lim <honwei189@gmail.com>

package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io/ioutil"
	"net/http"

	"github.com/tidwall/gjson"
	// "github.com/valyala/fastjson"
)

type Data []Datum

func UnmarshalData(data []byte) (Data, error) {
	var r Data
	err := json.Unmarshal(data, &r)
	return r, err
}

func (r *Data) Marshal() ([]byte, error) {
	return json.Marshal(r)
}

type Datum struct {
	FindUserUser []FindUserUser `json:"find:user_user"`
}

type FindUserUser struct {
	Name   string `json:"name"`
	Gender string `json:"gender"`
}

type PData struct {
	Data Person `json:"key"`
}
type Person struct {
	Name   string `json:"name"`
	Gender string `json:"gender"`
}

func main() {
	url := "http://localhost:8000"
	fmt.Println("URL:>", url)

	//json serialize
	post := "[{\"find\":\"user/user\",\"select\":\"name, gender\"}]"

	fmt.Println(url, "post", post)

	var jsonStr = []byte(post)
	fmt.Println("jsonStr", jsonStr)
	fmt.Println("new_str", bytes.NewBuffer(jsonStr))

	req, err := http.NewRequest("POST", url, bytes.NewBuffer(jsonStr))
	// req.Header.Set("X-Custom-Header", "myvalue")
	req.Header.Set("Content-Type", "application/json")

	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		panic(err)
	}
	defer resp.Body.Close()

	fmt.Println("response Status:", resp.Status)
	fmt.Println("response Headers:", resp.Header)
	body, _ := ioutil.ReadAll(resp.Body)
	fmt.Println("response Body:", string(body))

	// // Since we have a JSON array lets turn it into a Go array
	// var data []FindUserUser
	// json.Unmarshal(body, &data)

	// // Print what we got with keys
	// fmt.Printf("%+v\n", data)

	// // Loop over array and print some stuff we found
	// for _, e := range data {
	// 	// fmt.Printf("%v total %v cases: %v \n", e.Date, e.Status, e.Cases)
	// 	fmt.Printf("%v", e)
	// }

	// s := []byte(`{"foo": [123, "bar"]}`)
	// fmt.Printf("foo.0=%d\n", fastjson.GetInt(s, "foo", "0"))
	// fmt.Printf("foo.1=%s\n", fastjson.GetString(s, "foo", "1"))

	// var p fastjson.Parser
	// v, err := p.Parse(`{"aaa":"bbb","find:user_user":[{"name":"Administrator","gender":"M"}]}`)
	// if err != nil {
	// 	log.Fatal(err)
	// }
	// fmt.Printf("foo=%s\n", v.GetStringBytes("aaa"))
	// fmt.Printf("int=%d\n", v.GetInt("int"))
	// fmt.Printf("float=%f\n", v.GetFloat64("float"))
	// fmt.Printf("bool=%v\n", v.GetBool("bool"))
	// fmt.Printf("arr.1=%s\n", v.GetStringBytes("find:user_user", "0", "name"))

	// fmt.Printf("find:user_user.name=%s\n", fastjson.GetString(body, "find:user_user", "0", "name"))
	// fmt.Printf("find:user_user.gender=%s\n", fastjson.GetString(body, "find:user_user", "0", "gender"))

	// gjson.ForEachLine(string(body), func(line gjson.Result) bool {
	// 	// println(line.Get("find:user_user").String())
	// 	// println(line.String())

	// 	line.Get("find:user_user").ForEach(func(key, value gjson.Result) bool {
	// 		println(value.String())
	// 		println(value.Get("name").String())
	// 		return true // keep iterating
	// 	})
	// 	return true
	// })

	gjson.ForEachLine(string(body), func(line gjson.Result) bool {
		// println(line.Get("find:user_user").String())
		// println(line.String())

		line.ForEach(func(key, value gjson.Result) bool {
			fmt.Printf("\trequest = %s\n", key.String())

			line.Get(key.String()).ForEach(func(key1, value1 gjson.Result) bool {
				// fmt.Printf("\t  |-  data = %s\n", value1.String())
				fmt.Printf("\t\t  |-  name = %s, gender = %s\n", value1.Get("name").String(), value1.Get("gender").String())
				return true // keep iterating
			})
			return true // keep iterating
		})
		return true
	})
}

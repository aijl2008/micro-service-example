package net.artron.controller;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.client.RestTemplate;

@RestController
public class DemoJavaController {

    @Autowired
    private RestTemplate restTemplate;


    @GetMapping("/demo")
    public String request_shop_java_with_rest_template() {
        return this.restTemplate.getForObject("http://member-java/request_shop_java_with_rest_template", String.class);
    }
}



package net.artron.feign;

        import org.springframework.cloud.netflix.feign.FeignClient;
        import org.springframework.web.bind.annotation.RequestMapping;
        import org.springframework.web.bind.annotation.RequestMethod;

@FeignClient(name = "shop-sidecar", fallbackFactory = ShopSidecarFeignClientFallbackFactory.class)
public interface ShopSidecarFeignClient {
    @RequestMapping(value = "/goodses", method = RequestMethod.GET)
    public String goodses();
}

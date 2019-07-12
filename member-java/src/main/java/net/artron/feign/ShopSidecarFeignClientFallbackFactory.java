package net.artron.feign;

import feign.hystrix.FallbackFactory;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Component;

@Component
public class ShopSidecarFeignClientFallbackFactory implements FallbackFactory<ShopSidecarFeignClient> {
    private static final Logger LOGGER = LoggerFactory.getLogger(ShopSidecarFeignClientFallbackFactory.class);

    @Override
    public ShopSidecarFeignClient create(Throwable cause) {
        return new ShopSidecarFeignClient() {
            @Override
            public String goodses() {
                ShopSidecarFeignClientFallbackFactory.LOGGER.info("fallback; reason was:", cause);
                return "FeignClientFallbackFactory";
            }
        };
    }
}

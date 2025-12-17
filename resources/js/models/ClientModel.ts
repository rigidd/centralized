import { IdAndTimestamp } from "./IdAndTimestamp";

export interface ClientModel extends IdAndTimestamp {
  name: string;
  picture: string;
  display: boolean;
  redirect_urls: string[];
}

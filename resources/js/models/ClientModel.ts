import { IdAndTimestamp } from "./IdAndTimestamp";

export interface RedirectAlias {
  url: string;
  alias: string | null;
  icon: string | null;
}

export interface ClientModel extends IdAndTimestamp {
  name: string;
  picture: string;
  display: boolean;
  redirect_urls: RedirectAlias[];
}
